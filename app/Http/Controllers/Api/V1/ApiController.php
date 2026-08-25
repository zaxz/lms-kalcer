<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LmsAuthService;
use App\Services\LmsDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(
        protected LmsAuthService $auth,
        protected LmsDataService $lms,
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['sometimes', 'string', 'in:s,p,l,r'],
        ]);

        try {
            $result = $this->auth->login($data['student_id'], $data['password'], $data['role'] ?? 's');
            $profile = $this->lms->student();

            return $this->success([
                'token' => $request->session()->token(),
                'user' => [
                    'student_id' => $result['student_id'],
                    'name' => $profile['full_name'] ?? null,
                    'program' => $profile['program'] ?? null,
                ],
            ]);
        } catch (\App\Exceptions\LmsAuthenticationException $e) {
            return $this->error('INVALID_CREDENTIALS', $e->getMessage(), 401);
        } catch (\App\Exceptions\LmsSessionExpiredException $e) {
            return $this->error('LMS_UNAVAILABLE', $e->getMessage(), 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(['message' => 'Logout berhasil']);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->dashboard($refresh));
    }

    public function profile(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->profile($refresh));
    }

    public function schedule(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->schedule($refresh));
    }

    public function attendance(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        $raw = $this->lms->attendanceRaw($refresh);
        // PRD expects overall_average_percentage + courses, keep legacy keys for compatibility
        return $this->success([
            'overall_average_percentage' => $raw['overall_average_percentage'] ?? 0,
            'courses' => $raw['courses'] ?? [],
            // legacy aliases
            'avg' => $raw['overall_average_percentage'] ?? 0,
            'total_courses' => count($raw['courses'] ?? []),
            'critical' => count(array_filter($raw['courses'] ?? [], fn ($c) => ($c['status'] ?? null) === 'critical')),
        ]);
    }

    public function attendanceDetail(string $courseCode, string $classCode): JsonResponse
    {
        $client = $this->auth->restoreClient();
        $data = $this->auth->getCachedOrFetch(
            "lms:attendance:{$this->auth->getStudentId()}:{$courseCode}:{$classCode}",
            function () use ($client, $courseCode, $classCode) {
                $result = $client->getPage('/lecturing.php', [
                    'type' => 'kehadiran_detail',
                    'kdmk' => $courseCode,
                    'kdkelas' => $classCode,
                ]);
                if ($client->isSessionExpired($result['body'])) {
                    throw new \App\Exceptions\LmsSessionExpiredException('Sesi LMS Anda telah berakhir. Silakan login kembali.');
                }

                return (new \App\Services\Parsers\AttendanceParser($result['body']))->parseDetail();
            },
            (int) config('lms.cache_ttl.attendance_detail', 3600),
            (bool) request('refresh', false),
        );

        return $this->success($data);
    }

    public function exams(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->exams($refresh));
    }

    public function gradesCumulative(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        $raw = $this->lms->gradesRaw($refresh);
        return $this->success([
            'total_credits' => (int) ($raw['total_credits'] ?? 0),
            'total_weighted_credits' => (int) ($raw['total_weighted_credits'] ?? 0),
            'ipk' => (float) ($raw['ipk'] ?? 0),
            'courses' => $raw['courses'] ?? [],
            // aliases for web layer
            'total_sks' => (int) ($raw['total_credits'] ?? 0),
            'total_nxk' => (int) ($raw['total_weighted_credits'] ?? 0),
        ]);
    }

    public function gradesKhs(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        // PRD expects semesters array with academic_year, semester_name, etc.
        $raw = $this->lms->khsRaw($refresh);
        return $this->success($raw);
    }

    public function kmk(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->kmk($refresh));
    }

    public function pointbook(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->pointBook($refresh));
    }

    public function announcements(Request $request): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        $list = $this->lms->announcementList($refresh);
        if ($list === []) {
            $list = $this->lms->announcements($refresh);
        }
        return $this->success($list);
    }

    public function announcementDetail(Request $request, string $id): JsonResponse
    {
        $refresh = $request->boolean('refresh');
        return $this->success($this->lms->announcementDetail($id, $refresh));
    }

    protected function success(mixed $data, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'meta' => [
                'timestamp' => now('Asia/Jakarta')->toIso8601String(),
                'cached' => false,
                'last_synced' => now('Asia/Jakarta')->toIso8601String(),
            ],
            'data' => $data,
        ]);
    }

    protected function error(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => null,
            ],
        ], $status);
    }
}
