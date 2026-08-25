<?php

namespace App\Http\Controllers;

use App\Exceptions\LmsAuthenticationException;
use App\Exceptions\LmsSessionExpiredException;
use App\Services\LmsAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.login');
    }

    public function login(Request $request, LmsAuthService $auth): RedirectResponse
    {
        $credentials = $request->validate([
            'usid' => ['required', 'string'],
            'pwd' => ['required', 'string'],
            'role' => ['sometimes', 'string', 'in:s,p,l,r'],
        ]);

        try {
            $auth->login(
                (string) $credentials['usid'],
                (string) $credentials['pwd'],
                (string) ($credentials['role'] ?? 's'),
            );
        } catch (LmsAuthenticationException $e) {
            return Redirect::back()->withErrors(['usid' => $e->getMessage()])->withInput();
        } catch (LmsSessionExpiredException $e) {
            return Redirect::back()->withErrors(['usid' => $e->getMessage()])->withInput();
        }

        $request->session()->regenerate();

        // Warm cache in background after response (parallel fetch, non-blocking)
        try {
            $studentId = $auth->getStudentId();
            $jar = $request->session()->get('lms_cookie_jar', []);
            if ($studentId) {
                \App\Jobs\WarmLmsCacheJob::dispatch($studentId, $jar)->afterResponse();
            }
        } catch (\Throwable $e) {
            // Best effort
        }

        return Redirect::route('dashboard');
    }

    public function logout(Request $request, LmsAuthService $auth): RedirectResponse
    {
        $auth->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login');
    }
}
