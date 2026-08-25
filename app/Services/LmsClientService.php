<?php

namespace App\Services;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Session;

class LmsClientService
{
    protected Client $client;

    protected string $baseUrl;

    protected string $loginUrl;

    protected CookieJar $jar;

    public function __construct(?CookieJar $jar = null)
    {
        $this->baseUrl = rtrim((string) config('lms.base_url', 'https://lms.iwima.ac.id'), '/');
        $this->loginUrl = (string) config('lms.login_url', $this->baseUrl . '/index.php');
        $this->jar = $jar ?: new CookieJar();

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'allow_redirects' => true,
            'cookies' => $this->jar,
            'timeout' => (int) config('lms.timeout', 30),
            'headers' => [
                'User-Agent' => (string) config('lms.user_agent', 'Laravel LMS Companion/1.0'),
            ],
        ]);
    }

    public function getCookieJar(): CookieJar
    {
        return $this->jar;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function postLogin(string $studentId, string $password, string $role = 's'): array
    {
        $response = $this->client->request('POST', '/index.php', [
            'form_params' => [
                'usid' => $studentId,
                'pwd' => $password,
                'role' => $role,
                'action' => 'login',
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return [
            'body' => (string) $response->getBody(),
            'status' => $response->getStatusCode(),
            'redirected_to_main' => $this->redirectedTo('/main.php', $response),
        ];
    }

    public function getPage(string $path, array $query = []): array
    {
        $uri = $path . (empty($query) ? '' : '?' . http_build_query($query));
        $response = $this->client->request('GET', $uri);

        return [
            'body' => (string) $response->getBody(),
            'status' => $response->getStatusCode(),
            'redirected_to_login' => $this->redirectedTo('/index.php', $response),
        ];
    }

    public function getBinary(string $path): array
    {
        $response = $this->client->request('GET', $path);
        return [
            'body' => (string) $response->getBody(),
            'status' => $response->getStatusCode(),
            'content_type' => $response->getHeaderLine('Content-Type') ?: 'image/jpeg',
            'headers' => $response->getHeaders(),
        ];
    }

    public function getPageAsync(string $path, array $query = []): \GuzzleHttp\Promise\PromiseInterface
    {
        $uri = $path . (empty($query) ? '' : '?' . http_build_query($query));
        return $this->client->requestAsync('GET', $uri);
    }

    public function logout(string $sid): void
    {
        $this->client->request('GET', '/index.php', [
            'query' => [
                'action' => 'logout',
                'sid' => $sid,
            ],
        ]);
    }

    public function isSessionExpired(string $html): bool
    {
        // The site title is identical on every page, so only the login form
        // is a reliable signal that an authenticated page redirected to login.
        return str_contains($html, 'name="usid"');
    }

    public function hasInvalidCredentials(string $html): bool
    {
        return str_contains($html, 'ID atau password salah');
    }

    public function extractSessionIdFromCookies(): ?string
    {
        foreach ($this->jar->toArray() as $cookie) {
            $name = $cookie['Name'] ?? null;
            if (in_array($name, ['PHPSESSID', 'LMS_BACK'], true)) {
                return (string) ($cookie['Value'] ?? null);
            }
        }

        return null;
    }

    protected function redirectedTo(string $path, $response): bool
    {
        $path = '/' . ltrim($path, '/');

        if (method_exists($response, 'getHeaderLine')) {
            $history = $response->getHeaderLine('X-Guzzle-Redirect-History');
            $statuses = $response->getHeaderLine('X-Guzzle-Redirect-Status-History');

            if ($history) {
                foreach (explode(', ', $history) as $url) {
                    if (str_contains($url, $path)) {
                        return true;
                    }
                }

                return false;
            }
        }

        // Fallback: after a successful login Guzzle lands on the final URI, but
        // PSR-7 responses do not expose it. The caller can also detect success
        // via the absence of the login form / invalid-credentials message.
        return false;
    }
}
