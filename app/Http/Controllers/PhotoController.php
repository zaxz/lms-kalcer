<?php

namespace App\Http\Controllers;

use App\Services\LmsAuthService;
use App\Services\LmsDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoController extends Controller
{
    public function __invoke(Request $request, LmsDataService $lms, LmsAuthService $auth)
    {
        // Ensure user is authenticated via middleware, but double-check
        $profile = $lms->profile();
        $photoPath = $profile['photo_path'] ?? null;

        if (!$photoPath) {
            // Fallback construct from student_id
            $studentId = $auth->getStudentId() ?? $profile['nim'] ?? null;
            if ($studentId && preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $studentId, $m)) {
                $photoPath = "uploads/photos/student/{$m[1]}/{$m[2]}/{$studentId}.JPG";
            }
        }

        if (!$photoPath) {
            abort(404);
        }

        // Normalize to absolute path for LmsClientService
        $path = '/' . ltrim($photoPath, '/');
        $studentId = $auth->getStudentId() ?? $profile['nim'] ?? 'unknown';
        $cacheKey = "lms:photo:{$studentId}";

        try {
            $cached = Cache::get($cacheKey);
            if ($cached && isset($cached['body'])) {
                return response($cached['body'], 200, [
                    'Content-Type' => $cached['content_type'] ?? 'image/jpeg',
                    'Cache-Control' => 'public, max-age=86400',
                    'Content-Length' => strlen($cached['body']),
                    'X-Cache' => 'HIT',
                ]);
            }

            $client = $auth->restoreClient();
            $result = $client->getBinary($path);

            if (($result['status'] ?? 200) >= 400) {
                abort(404);
            }

            $body = $result['body'] ?? '';
            // If LMS returned login page instead of image, detect
            if (str_contains($body, 'name="usid"') || strlen($body) < 1000) {
                $ct = $result['content_type'] ?? '';
                if (str_contains($ct, 'text/html')) {
                    abort(404);
                }
            }

            $contentType = $result['content_type'] ?? 'image/jpeg';
            // Cache binary in Laravel cache for 24h and browser cache
            Cache::put($cacheKey, ['body' => $body, 'content_type' => $contentType], 86400);
            return response($body, 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=86400',
                'Content-Length' => strlen($body),
                'X-Cache' => 'MISS',
            ]);
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
