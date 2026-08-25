<?php

namespace App\Http\Controllers;

use App\Services\LmsDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, LmsDataService $lms): View
    {
        $refresh = $request->boolean('refresh');
        // Concurrent fetch for dashboard-critical data (5x faster on cache miss)
        $data = $lms->dashboardDataConcurrent($refresh);
        $todayName = $lms->todayName();
        $schedule = $data['schedule'] ?? [];
        $student = $data['student'] ?? $lms->student($refresh);
        $dashboard = $data['dashboard'] ?? [];
        $summary = $dashboard['academic_summary'] ?? $lms->academicSummary($refresh);
        $attendanceRaw = $data['attendance'] ?? ['overall_average_percentage' => 0, 'courses' => []];
        $attendanceAvg = $attendanceRaw['overall_average_percentage'] ?? 0;
        if (is_array($attendanceRaw) && isset($attendanceRaw['courses'])) {
            $attendanceAvg = $attendanceRaw['overall_average_percentage'] ?? 0;
        } else {
            $attendanceAvg = $lms->attendanceSummary($refresh)['avg'] ?? 0;
        }
        $attendanceStatus = $attendanceAvg >= 85 ? 'safe' : 'warning';
        $hour = (int) now('Asia/Jakarta')->format('G');
        $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));

        // Upcoming exams derived from concurrent exams data
        $examsRaw = $data['exams'] ?? $lms->exams($refresh);
        $upcomingExams = collect($examsRaw)->flatten(1)->filter(fn ($exam) => ($exam['date'] ?? '') >= date('Y-m-d'))->sortBy('date')->values()->take(2)->all();
        $announcements = $dashboard['announcements'] ?? $lms->announcements($refresh);

        return view('pages.dashboard', [
            'isCached' => ! $refresh && $lms->isDashboardCached(),
            'student' => $student,
            'summary' => [
                'ipk' => $summary['ipk'] ?? '',
                'total_sks' => $summary['total_sks'] ?? 0,
                'semester' => $summary['semester'] ?? '',
                'tahun_akademik' => $summary['tahun_akademik'] ?? '',
                'attendance_avg' => $attendanceAvg,
            ],
            'todayName' => $todayName,
            'todaySchedule' => $schedule[$todayName] ?? [],
            'greeting' => $greeting,
            'attendanceStatus' => $attendanceStatus,
            'upcomingExams' => $upcomingExams,
            'announcements' => $announcements,
            'updatedAt' => $lms->updatedAt(),
        ]);
    }
}
