<?php

namespace App\Services;

use App\Exceptions\LmsAuthenticationException;
use App\Exceptions\LmsSessionExpiredException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class LmsAuthService
{
    protected LmsClientService $client;

    public function __construct(LmsClientService $client)
    {
        $this->client = $client;
    }

    public function login(string $studentId, string $password, string $role = 's'): array
    {
        $password = (string) $password;

        try {
            $result = $this->client->postLogin($studentId, $password, $role);
        } catch (RequestException $e) {
            throw new LmsSessionExpiredException('Gagal menghubungi server LMS.');
        } finally {
            // Credentials must never outlive the request.
            $password = str_repeat('*', strlen($password));
        }

        $body = (string) $result['body'];

        if ($this->client->hasInvalidCredentials($body)) {
            throw new LmsAuthenticationException('ID atau password salah.');
        }

        // A successful login redirects away from the login form. Since PSR-7
        // responses do not expose the effective URL, success is "no login form
        // and no invalid-credentials message" after POSTing credentials.
        if ($this->client->isSessionExpired($body)) {
            throw new LmsAuthenticationException('ID atau password salah.');
        }

        Session::put('lms_cookie_jar', $this->client->getCookieJar()->toArray());
        Session::put('lms_student_id', $studentId);
        Session::save();

        return [
            'student_id' => $studentId,
            'session_active' => true,
        ];
    }

    public function logout(): void
    {
        $studentId = Session::get('lms_student_id');
        $client = $this->restoreClient();

        $sid = $client->extractSessionIdFromCookies();
        if ($sid) {
            try {
                $client->logout($sid);
            } catch (RequestException $e) {
                // Ignore failures during remote logout.
            }
        }

        Session::forget(['lms_cookie_jar', 'lms_student_id']);
        Session::save();

        if ($studentId) {
            $this->flushUserCache($studentId);
        }
    }

    public function isAuthenticated(): bool
    {
        return Session::has('lms_cookie_jar') && Session::has('lms_student_id');
    }

    public function getStudentId(): ?string
    {
        return Session::get('lms_student_id');
    }

    public function restoreClient(): LmsClientService
    {
        $cookies = Session::get('lms_cookie_jar');

        if (is_array($cookies) && $cookies !== []) {
            $jar = new CookieJar(false, array_map(
                fn (array $cookie) => new \GuzzleHttp\Cookie\SetCookie($cookie),
                $cookies,
            ));

            return new LmsClientService($jar);
        }

        return new LmsClientService();
    }

    public function getCachedOrFetch(string $cacheKey, callable $fetchFn, int $ttl, bool $forceRefresh = false): mixed
    {
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $ttl, $fetchFn);
    }

    public function flushUserCache(string $studentId): void
    {
        $keys = [
            "lms:profile:{$studentId}",
            "lms:dashboard:{$studentId}",
            "lms:schedule:{$studentId}",
            "lms:attendance:{$studentId}",
            "lms:exams:{$studentId}",
            "lms:grades:khs:{$studentId}",
            "lms:grades:cumulative:{$studentId}",
            "lms:kmks:{$studentId}",
            "lms:pointbook:{$studentId}",
            "lms:announcements:list:{$studentId}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
        // Detail keys are per-id: lms:announcements:detail:{id}:{studentId} - clear via pattern if driver supports tags, otherwise they expire via TTL
    }
}
