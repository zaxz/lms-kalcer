<?php

return [
    'base_url' => env('LMS_BASE_URL', 'https://lms.iwima.ac.id'),
    'login_url' => env('LMS_LOGIN_URL', 'https://lms.iwima.ac.id/index.php'),
    'logout_url' => env('LMS_LOGOUT_URL', 'https://lms.iwima.ac.id/index.php'),
    'timeout' => (int) env('LMS_TIMEOUT', 30),
    'user_agent' => env('LMS_USER_AGENT', 'Laravel LMS Companion/1.0'),

    'cache_ttl' => [
        'profile' => (int) env('LMS_CACHE_PROFILE', 86400),
        'dashboard' => (int) env('LMS_CACHE_DASHBOARD', 1800),
        'schedule' => (int) env('LMS_CACHE_SCHEDULE', 43200),
        'attendance' => (int) env('LMS_CACHE_ATTENDANCE', 3600),
        'attendance_detail' => (int) env('LMS_CACHE_ATTENDANCE_DETAIL', 3600),
        'exams' => (int) env('LMS_CACHE_EXAMS', 7200),
        'grades_khs' => (int) env('LMS_CACHE_GRADES_KHS', 21600),
        'grades_cumulative' => (int) env('LMS_CACHE_GRADES_CUMULATIVE', 21600),
        'kmk' => (int) env('LMS_CACHE_KMK', 43200),
        'pointbook' => (int) env('LMS_CACHE_POINTBOOK', 7200),
    ],
];
