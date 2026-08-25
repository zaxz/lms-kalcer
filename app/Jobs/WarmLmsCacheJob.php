<?php

namespace App\Jobs;

use App\Services\LmsDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WarmLmsCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $studentId, public array $cookieJar) {}

    public function handle(): void
    {
        // Restore session for this job
        \Illuminate\Support\Facades\Session::put('lms_cookie_jar', $this->cookieJar);
        \Illuminate\Support\Facades\Session::put('lms_student_id', $this->studentId);
        \Illuminate\Support\Facades\Session::save();

        try {
            app(LmsDataService::class)->warmCache();
        } catch (\Throwable $e) {
            // Best effort
        }
    }
}
