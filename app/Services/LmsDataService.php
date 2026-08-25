<?php

namespace App\Services;

use App\Exceptions\LmsSessionExpiredException;
use App\Services\Parsers\AttendanceParser;
use App\Services\Parsers\DashboardParser;
use App\Services\Parsers\ExamScheduleParser;
use App\Services\Parsers\GradeParser;
use App\Services\Parsers\KmksParser;
use App\Services\Parsers\PointBookParser;
use App\Services\Parsers\ProfileParser;
use App\Services\Parsers\ScheduleParser;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Cache;

class LmsDataService
{
    public function __construct(protected LmsAuthService $auth)
    {
    }

    public function profile(bool $refresh = false): array
    {
        return $this->fetch('profile', '/profile.php', [], fn ($html) => (new ProfileParser($html))->parse(), $this->ttl('profile'), $refresh);
    }

    public function student(bool $refresh = false): array
    {
        $profile = $this->profile($refresh);

        return [
            'student_id' => $profile['nim'] ?? null,
            'full_name' => $profile['name'] ?? null,
            'program' => $profile['program'] ?? null,
            'concentration' => $profile['concentration'] ?? null,
            'class' => $profile['class'] ?? null,
            'cohort_year' => $profile['cohort_year'] ?? null,
            'academic_advisor' => $profile['academic_advisor'] ?? null,
            'photo_url' => $profile['photo_url'] ?? null,
            'photo_path' => $profile['photo_path'] ?? null,
        ];
    }

    public function dashboard(bool $refresh = false): array
    {
        return $this->fetch('dashboard', '/main.php', [], fn ($html) => (new DashboardParser($html))->parse(), $this->ttl('dashboard'), $refresh);
    }

    public function academicSummary(bool $refresh = false): array
    {
        return $this->dashboard($refresh)['academic_summary'] ?? [
            'tahun_akademik' => '',
            'semester' => '',
            'ipk' => '',
            'total_sks' => 0,
        ];
    }

    public function announcements(bool $refresh = false): array
    {
        return $this->dashboard($refresh)['announcements'] ?? [];
    }

    public function announcementList(bool $refresh = false): array
    {
        return $this->fetch('announcements:list', '/main.php', ['type' => 'news'], fn ($html) => (new DashboardParser($html))->parseAnnouncementList(), $this->ttl('dashboard'), $refresh);
    }

    public function announcementDetail(string $id, bool $refresh = false): array
    {
        $data = $this->fetch("announcements:detail:{$id}", '/main.php', ['type' => 'news', 'id' => $id], fn ($html) => (new DashboardParser($html))->parseAnnouncementDetail(), $this->ttl('dashboard'), $refresh);
        $data['id'] = $id;
        return $data;
    }

    public function schedule(bool $refresh = false): array
    {
        return $this->fetch('schedule', '/lecturing.php', ['type' => 'jadwal'], fn ($html) => (new ScheduleParser($html))->parse(), $this->ttl('schedule'), $refresh);
    }

    public function todaySchedule(bool $refresh = false): array
    {
        $schedule = $this->schedule($refresh);

        return $schedule[$this->todayName()] ?? [];
    }

    public function attendance(bool $refresh = false): array
    {
        $data = $this->fetch('attendance', '/lecturing.php', ['type' => 'kehadiran'], fn ($html) => (new AttendanceParser($html))->parseSummary(), $this->ttl('attendance'), $refresh);

        return $data['courses'] ?? [];
    }

    public function attendanceRaw(bool $refresh = false): array
    {
        return $this->fetch('attendance', '/lecturing.php', ['type' => 'kehadiran'], fn ($html) => (new AttendanceParser($html))->parseSummary(), $this->ttl('attendance'), $refresh);
    }

    public function attendanceSummary(bool $refresh = false): array
    {
        $items = $this->attendance($refresh);
        $percentages = array_column($items, 'percentage');

        return [
            'avg' => $percentages === [] ? 0.0 : round(array_sum($percentages) / count($percentages), 1),
            'total_courses' => count($items),
            'critical' => count(array_filter($items, fn ($item) => ($item['status'] ?? null) === 'critical')),
        ];
    }

    public function exams(bool $refresh = false): array
    {
        $data = $this->fetch('exams', '/scoring.php', ['type' => 'ujian'], fn ($html) => (new ExamScheduleParser($html))->parse(), $this->ttl('exams'), $refresh);

        return [
            'UTS' => $data['uts'] ?? [],
            'UAS' => $data['uas'] ?? [],
        ];
    }

    public function upcomingExams(int $limit = 2, bool $refresh = false): array
    {
        return collect($this->exams($refresh))
            ->flatten(1)
            ->filter(fn ($exam) => ($exam['date'] ?? '') >= date('Y-m-d'))
            ->sortBy('date')
            ->values()
            ->take($limit)
            ->all();
    }

    public function grades(bool $refresh = false): array
    {
        return $this->fetch('grades:cumulative', '/scoring.php', ['type' => 'daftar'], fn ($html) => (new GradeParser($html))->parseCumulative(), $this->ttl('grades_cumulative'), $refresh)['courses'] ?? [];
    }

    public function gradesRaw(bool $refresh = false): array
    {
        return $this->fetch('grades:cumulative', '/scoring.php', ['type' => 'daftar'], fn ($html) => (new GradeParser($html))->parseCumulative(), $this->ttl('grades_cumulative'), $refresh);
    }

    public function gradesSummary(bool $refresh = false): array
    {
        $data = $this->fetch('grades:cumulative', '/scoring.php', ['type' => 'daftar'], fn ($html) => (new GradeParser($html))->parseCumulative(), $this->ttl('grades_cumulative'), $refresh);

        return [
            'ipk' => (float) ($data['ipk'] ?? 0),
            'total_sks' => (int) ($data['total_credits'] ?? 0),
            'total_nxk' => (int) ($data['total_weighted_credits'] ?? 0),
            // PRD aliases
            'total_credits' => (int) ($data['total_credits'] ?? 0),
            'total_weighted_credits' => (int) ($data['total_weighted_credits'] ?? 0),
        ];
    }

    public function khs(bool $refresh = false): array
    {
        $data = $this->fetch('grades:khs', '/scoring.php', ['type' => 'laporan'], fn ($html) => (new GradeParser($html))->parseKhs(), $this->ttl('grades_khs'), $refresh);
        return array_map(function (array $semester, int $index) {
            $num = $semester['semester_number'] ?? ($index + 1);
            return [
                // Web-friendly keys (existing)
                'semester' => 'Semester ' . $num,
                'items' => $semester['courses'] ?? [],
                'courses' => $semester['courses'] ?? [],
                'total_sks' => $semester['total_credits'] ?? 0,
                'total_nxk' => $semester['total_weighted_credits'] ?? 0,
                'ips' => (float) ($semester['ip_semester'] ?? 0),
                // PRD-compliant keys
                'academic_year' => $semester['academic_year'] ?? null,
                'semester_name' => $semester['semester_name'] ?? null,
                'semester_number' => $semester['semester_number'] ?? $num,
                'total_credits' => $semester['total_credits'] ?? 0,
                'total_weighted_credits' => $semester['total_weighted_credits'] ?? 0,
                'ip_semester' => (float) ($semester['ip_semester'] ?? 0),
            ];
        }, $data['semesters'] ?? [], array_keys($data['semesters'] ?? []));
    }

    public function khsRaw(bool $refresh = false): array
    {
        return $this->fetch('grades:khs', '/scoring.php', ['type' => 'laporan'], fn ($html) => (new GradeParser($html))->parseKhs(), $this->ttl('grades_khs'), $refresh);
    }

    public function kmk(bool $refresh = false): array
    {
        return $this->fetch('kmks', '/registration.php', ['type' => 'kmk'], fn ($html) => (new KmksParser($html))->parse(), $this->ttl('kmk'), $refresh);
    }

    public function pointBook(bool $refresh = false): array
    {
        $data = $this->fetch('pointbook', '/history.php', ['type' => 'pointbook'], fn ($html) => (new PointBookParser($html))->parse(), $this->ttl('pointbook'), $refresh);

        return [
            'entries' => array_map(fn (array $entry) => [
                'date' => $entry['date'],
                'activity' => $entry['activity_name'],
                'activity_name' => $entry['activity_name'],
                'points' => $entry['points'],
                'note' => $entry['note'],
            ], $data['entries'] ?? []),
            'total_points' => (int) ($data['total_points'] ?? 0),
        ];
    }

    public function todayName(): string
    {
        return match ((int) \Carbon\Carbon::now('Asia/Jakarta')->format('N')) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
    }

    public function isCached(string $key): bool
    {
        try {
            $studentId = $this->requireStudentId();
            return Cache::has("lms:{$key}:{$studentId}");
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function isDashboardCached(): bool
    {
        return $this->isCached('dashboard') && $this->isCached('schedule') && $this->isCached('attendance');
    }

    public function updatedAt(): string
    {
        return 'Diperbarui pukul ' . now('Asia/Jakarta')->format('H:i') . ' WIB';
    }

    protected function fetch(string $key, string $path, array $query, callable $parser, int $ttl, bool $refresh = false): mixed
    {
        $studentId = $this->requireStudentId();
        $cacheKey = "lms:{$key}:{$studentId}";

        return $this->auth->getCachedOrFetch($cacheKey, function () use ($path, $query, $parser) {
            $client = $this->auth->restoreClient();
            $result = $client->getPage($path, $query);

            if ($client->isSessionExpired($result['body'])) {
                throw new LmsSessionExpiredException('Sesi LMS Anda telah berakhir. Silakan login kembali.');
            }

            return $parser($result['body']);
        }, $ttl, $refresh);
    }

    /**
     * Fetch multiple LMS pages concurrently if not cached.
     * $jobs: ['cacheKey' => ['path'=>string, 'query'=>array, 'parser'=>callable, 'ttl'=>int]]
     */
    protected function fetchConcurrent(array $jobs, bool $refresh = false): array
    {
        $studentId = $this->requireStudentId();
        $results = [];
        $promises = [];
        $client = $this->auth->restoreClient();

        foreach ($jobs as $cacheKey => $job) {
            $fullKey = "lms:{$cacheKey}:{$studentId}";
            if (!$refresh && Cache::has($fullKey)) {
                $results[$cacheKey] = Cache::get($fullKey);
                continue;
            }
            $path = $job['path'];
            $query = $job['query'] ?? [];
            $promises[$cacheKey] = $client->getPageAsync($path, $query)->then(
                function ($response) use ($job, $fullKey) {
                    $body = (string) $response->getBody();
                    if (str_contains($body, 'name="usid"')) {
                        throw new LmsSessionExpiredException('Sesi LMS Anda telah berakhir. Silakan login kembali.');
                    }
                    $data = ($job['parser'])($body);
                    Cache::put($fullKey, $data, $job['ttl']);
                    return $data;
                }
            );
        }

        if ($promises !== []) {
            $settled = Utils::settle($promises)->wait();
            foreach ($settled as $key => $state) {
                if ($state['state'] === 'fulfilled') {
                    $results[$key] = $state['value'];
                } else {
                    // On failure, try fallback to cache if exists
                    $fullKey = "lms:{$key}:{$studentId}";
                    if (Cache::has($fullKey)) {
                        $results[$key] = Cache::get($fullKey);
                    } else {
                        throw $state['reason'];
                    }
                }
            }
        }

        return $results;
    }

    public function dashboardDataConcurrent(bool $refresh = false): array
    {
        $jobs = [
            'dashboard' => ['path' => '/main.php', 'query' => [], 'parser' => fn ($html) => (new DashboardParser($html))->parse(), 'ttl' => $this->ttl('dashboard')],
            'profile' => ['path' => '/profile.php', 'query' => [], 'parser' => fn ($html) => (new ProfileParser($html))->parse(), 'ttl' => $this->ttl('profile')],
            'schedule' => ['path' => '/lecturing.php', 'query' => ['type' => 'jadwal'], 'parser' => fn ($html) => (new ScheduleParser($html))->parse(), 'ttl' => $this->ttl('schedule')],
            'attendance' => ['path' => '/lecturing.php', 'query' => ['type' => 'kehadiran'], 'parser' => fn ($html) => (new AttendanceParser($html))->parseSummary(), 'ttl' => $this->ttl('attendance')],
            'exams' => ['path' => '/scoring.php', 'query' => ['type' => 'ujian'], 'parser' => fn ($html) => (new ExamScheduleParser($html))->parse(), 'ttl' => $this->ttl('exams')],
        ];

        $data = $this->fetchConcurrent($jobs, $refresh);

        // Ensure student derived from profile
        $profile = $data['profile'] ?? $this->profile($refresh);
        $student = [
            'student_id' => $profile['nim'] ?? null,
            'full_name' => $profile['name'] ?? null,
            'program' => $profile['program'] ?? null,
            'concentration' => $profile['concentration'] ?? null,
            'class' => $profile['class'] ?? null,
            'cohort_year' => $profile['cohort_year'] ?? null,
            'academic_advisor' => $profile['academic_advisor'] ?? null,
            'photo_url' => $profile['photo_url'] ?? null,
            'photo_path' => $profile['photo_path'] ?? null,
        ];

        return [
            'dashboard' => $data['dashboard'] ?? ['academic_summary' => [], 'announcements' => []],
            'profile' => $profile,
            'student' => $student,
            'schedule' => $data['schedule'] ?? [],
            'attendance' => $data['attendance'] ?? ['overall_average_percentage' => 0, 'courses' => []],
            'exams' => $data['exams'] ?? ['uts' => [], 'uas' => []],
        ];
    }

    public function warmCache(): void
    {
        try {
            $this->dashboardDataConcurrent(false);
            // Also warm grades and kmk in background (non-blocking for dashboard)
            $jobs = [
                'grades:cumulative' => ['path' => '/scoring.php', 'query' => ['type' => 'daftar'], 'parser' => fn ($html) => (new GradeParser($html))->parseCumulative(), 'ttl' => $this->ttl('grades_cumulative')],
                'grades:khs' => ['path' => '/scoring.php', 'query' => ['type' => 'laporan'], 'parser' => fn ($html) => (new GradeParser($html))->parseKhs(), 'ttl' => $this->ttl('grades_khs')],
                'kmks' => ['path' => '/registration.php', 'query' => ['type' => 'kmk'], 'parser' => fn ($html) => (new KmksParser($html))->parse(), 'ttl' => $this->ttl('kmk')],
                'pointbook' => ['path' => '/history.php', 'query' => ['type' => 'pointbook'], 'parser' => fn ($html) => (new PointBookParser($html))->parse(), 'ttl' => $this->ttl('pointbook')],
            ];
            $this->fetchConcurrent($jobs, false);
        } catch (\Throwable $e) {
            // Warming is best-effort, ignore failures
        }
    }

    protected function ttl(string $key): int
    {
        return (int) (config('lms.cache_ttl.' . $key, 3600));
    }

    protected function requireStudentId(): string
    {
        $studentId = $this->auth->getStudentId();
        if ($studentId === null) {
            throw new LmsSessionExpiredException('Sesi LMS Anda telah berakhir. Silakan login kembali.');
        }

        return $studentId;
    }
}
