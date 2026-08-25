<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ProfileParser extends AbstractParser
{
    protected array $fieldMap = [
        'NIM' => 'nim',
        'No. KTP' => 'ktp',
        'Nama lengkap' => 'name',
        'Program studi' => 'program',
        'Konsentrasi' => 'concentration',
        'Kelas' => 'class',
        'Angkatan' => 'cohort_year',
        'Dosen PA' => 'academic_advisor',
        'Alamat' => 'address',
        'Kota' => 'city',
        'Jender' => 'gender',
        'Tempat lahir' => 'birth_place',
        'Tanggal lahir' => 'birth_date',
        'Kewarganegaraan' => 'nationality',
        'Agama' => 'religion',
        'Status marital' => 'marital_status',
        'Akun Google Workspace' => 'google_workspace_account',
    ];

    public function parse(): array
    {
        $profile = array_fill_keys(array_values($this->fieldMap), null);
        $profile['photo_url'] = null;
        $profile['photo_path'] = null;

        $this->crawler->filter('table tr')->each(function (Crawler $row) use (&$profile) {
            $label = $this->cleanText($row->filter('th')->count() ? $row->filter('th')->first()->text() : '');
            if ($label === '' || ! array_key_exists($label, $this->fieldMap)) {
                return;
            }

            $value = $row->filter('td')->count() ? $this->cleanText($row->filter('td')->first()->text()) : '';

            $field = $this->fieldMap[$label];
            if ($field === 'birth_date') {
                $profile[$field] = $this->normalizeDate($value);
            } elseif ($field === 'ktp') {
                $profile[$field] = $this->nullIfEmpty($value);
            } else {
                $profile[$field] = $value === '' ? null : $value;
            }
        });

        // Extract avatar photo from LMS (e.g. ./uploads/photos/student/25/230/25.230.0025.JPG)
        $photoSrc = null;
        $this->crawler->filter('img')->each(function (Crawler $img) use (&$photoSrc) {
            if ($photoSrc !== null) return;
            $src = $img->attr('src');
            if ($src && str_contains($src, 'uploads/photos')) {
                $photoSrc = $src;
            }
        });
        $baseUrl = 'https://lms.iwima.ac.id';
        try {
            $cfg = config('lms.base_url', $baseUrl);
            if (is_string($cfg) && $cfg !== '') $baseUrl = $cfg;
        } catch (\Throwable $e) {}

        if ($photoSrc !== null) {
            $normalized = $this->normalizePhotoPath($photoSrc);
            $profile['photo_path'] = $normalized;
            $profile['photo_url'] = rtrim($baseUrl, '/') . '/' . ltrim($normalized, '/');
        } elseif (!empty($profile['nim'])) {
            // Fallback construct from NIM: 25.230.0025 -> 25/230/25.230.0025.JPG
            $nim = trim((string) $profile['nim']);
            if (preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $nim, $m)) {
                $path = "uploads/photos/student/{$m[1]}/{$m[2]}/{$nim}.JPG";
                $profile['photo_path'] = $path;
                $profile['photo_url'] = rtrim($baseUrl, '/') . '/' . $path;
            }
        }

        return $profile;
    }

    protected function normalizePhotoPath(string $src): string
    {
        $src = trim($src);
        // Remove leading ./ or /
        $src = preg_replace('#^\./#', '', $src);
        $src = ltrim($src, '/');
        return $src;
    }
}
