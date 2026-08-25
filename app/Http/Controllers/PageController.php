<?php

namespace App\Http\Controllers;

use App\Services\LmsDataService;

class PageController extends Controller
{
    public function __construct(protected LmsDataService $lms)
    {
    }

    public function jadwal()
    {
        $refresh = request()->boolean('refresh');
        $schedule = $this->lms->schedule($refresh);
        $todayName = $this->lms->todayName();

        return view('pages.jadwal', [
            'isCached' => ! $refresh && $this->lms->isCached('schedule'),
            'schedule' => $schedule,
            'summary' => $this->lms->academicSummary($refresh),
            'todayName' => $todayName,
            'activeDay' => array_key_exists($todayName, $schedule) ? $todayName : (array_key_first($schedule) ?: $todayName),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function ujian()
    {
        $refresh = request()->boolean('refresh');
        return view('pages.ujian', [
            'isCached' => ! $refresh && $this->lms->isCached('exams'),
            'exams' => $this->lms->exams($refresh),
            'summary' => $this->lms->academicSummary($refresh),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function nilai()
    {
        $refresh = request()->boolean('refresh');
        $gradesSummary = $this->lms->gradesSummary($refresh);
        $khs = $this->lms->khs($refresh);
        $currentSemester = count($khs) > 0 ? ($khs[count($khs)-1]['semester_number'] ?? count($khs)) : null;

        return view('pages.nilai', [
            'isCached' => ! $refresh && $this->lms->isCached('grades:cumulative'),
            'grades' => $this->lms->grades($refresh),
            'gradesSummary' => $gradesSummary,
            'ipkFormatted' => format_number((float) $gradesSummary['ipk'], 2),
            'currentSemester' => $currentSemester,
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function khs()
    {
        $refresh = request()->boolean('refresh');
        return view('pages.khs', [
            'isCached' => ! $refresh && $this->lms->isCached('grades:khs'),
            'khs' => $this->lms->khs($refresh),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function kehadiran()
    {
        $refresh = request()->boolean('refresh');
        $summary = $this->lms->academicSummary($refresh);
        // Derive current semester number from KHS (most reliable per PRD §5.7), fallback to summary
        $khs = $this->lms->khs($refresh);
        $currentSemester = null;
        if ($khs !== []) {
            $last = end($khs);
            $currentSemester = $last['semester_number'] ?? count($khs);
        }
        // If KHS empty, try to infer from summary semester name (Genap=2, Ganjil=1) or numeric
        if ($currentSemester === null && !empty($summary['semester'])) {
            $sem = trim((string) $summary['semester']);
            if (is_numeric($sem)) {
                $currentSemester = (int) $sem;
            } elseif (strcasecmp($sem, 'Genap') === 0) {
                $currentSemester = 2;
            } elseif (strcasecmp($sem, 'Ganjil') === 0) {
                $currentSemester = 1;
            }
        }

        return view('pages.kehadiran', [
            'isCached' => ! $refresh && $this->lms->isCached('attendance'),
            'attendance' => $this->lms->attendance($refresh),
            'attendanceSummary' => $this->lms->attendanceSummary($refresh),
            'summary' => $summary,
            'currentSemester' => $currentSemester,
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function profil()
    {
        $refresh = request()->boolean('refresh');
        return view('pages.profil', [
            'isCached' => ! $refresh && $this->lms->isCached('profile'),
            'student' => $this->lms->student($refresh),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function kmk()
    {
        $refresh = request()->boolean('refresh');
        return view('pages.kmk', [
            'isCached' => ! $refresh && $this->lms->isCached('kmks'),
            'kmk' => $this->lms->kmk($refresh),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function pointBook()
    {
        $refresh = request()->boolean('refresh');
        return view('pages.point-book', [
            'isCached' => ! $refresh && $this->lms->isCached('pointbook'),
            'pointBook' => $this->lms->pointBook($refresh),
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function pengumuman()
    {
        $refresh = request()->boolean('refresh');
        // Prefer full list from /main.php?type=news, fallback to dashboard 5 if empty
        $list = $this->lms->announcementList($refresh);
        if ($list === []) {
            $list = $this->lms->announcements($refresh);
        }
        return view('pages.pengumuman', [
            'isCached' => ! $refresh && ($this->lms->isCached('announcements:list') || $this->lms->isCached('dashboard')),
            'announcements' => $list,
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }

    public function pengumumanDetail(string $id)
    {
        $refresh = request()->boolean('refresh');
        $detail = $this->lms->announcementDetail($id, $refresh);
        return view('pages.pengumuman-detail', [
            'isCached' => ! $refresh && $this->lms->isCached("announcements:detail:{$id}"),
            'announcement' => $detail,
            'updatedAt' => $this->lms->updatedAt(),
        ]);
    }
}
