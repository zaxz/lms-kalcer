<?php

namespace App\Http\Middleware;

use App\Services\LmsAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLmsSessionValid
{
    public function __construct(protected LmsAuthService $auth)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->auth->isAuthenticated()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'LMS_SESSION_EXPIRED',
                        'message' => 'Sesi LMS Anda telah berakhir. Silakan login kembali.',
                        'details' => null,
                    ],
                ], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
