<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractParser
{
    protected Crawler $crawler;

    public function __construct(string $html)
    {
        $this->crawler = new Crawler($html);
    }

    protected function cleanText(?string $text): string
    {
        $text = preg_replace('/\s+/', ' ', (string) $text) ?? '';

        return trim($text);
    }

    protected function normalizeDecimal(string $value): float
    {
        $normalized = str_replace([',', ' '], ['.', ''], trim($value));

        return (float) $normalized;
    }

    protected function normalizeInt(string $value): int
    {
        return (int) preg_replace('/\D/', '', trim($value));
    }

    protected function normalizeTime(string $time): string
    {
        $time = trim($time);
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }

        return $time;
    }

    protected function normalizeDate(string $date): ?string
    {
        $date = trim($date);
        if ($date === '' || $date === '-') {
            return null;
        }

        $formats = ['d/m/Y', 'd-m-Y', 'd F Y', 'j F Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $date);
            $errors = \DateTime::getLastErrors();
            if ($dt && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) && $dt->format($format) === $date) {
                return $dt->format('Y-m-d');
            }
        }

        return $date;
    }

    protected function nullIfEmpty(?string $value): ?string
    {
        $value = $this->cleanText($value);

        return $value === '' || $value === '-' ? null : $value;
    }

    protected function heading(string $text, string $tag = 'h2,h3,h4,h5'): ?Crawler
    {
        foreach ($this->crawler->filter($tag) as $node) {
            $crawler = new Crawler($node);
            if ($this->cleanText($crawler->text()) === $this->cleanText($text)) {
                return $crawler;
            }
        }

        return null;
    }

    protected function firstHeadingText(string $regex): ?string
    {
        foreach ($this->crawler->filter('h2,h3,h4,h5') as $node) {
            $text = $this->cleanText((new Crawler($node))->text());
            if (preg_match($regex, $text, $m)) {
                return $m[1] ?? $text;
            }
        }

        return null;
    }

    protected function tableHeaders(Crawler $table): array
    {
        $headers = [];
        $table->filter('thead th')->each(function (Crawler $th) use (&$headers) {
            $headers[] = $this->cleanText($th->text());
        });

        if ($headers === []) {
            $table->filter('th')->each(function (Crawler $th) use (&$headers) {
                $headers[] = $this->cleanText($th->text());
            });
        }

        return $headers;
    }

    protected function tableRows(Crawler $table): \Illuminate\Support\Collection
    {
        $rows = [];
        $table->filter('tbody tr')->each(function (Crawler $row) use (&$rows) {
            $cells = [];
            $row->filter('td')->each(function (Crawler $td) use (&$cells) {
                $cells[] = $this->cleanText($td->text());
            });
            if ($cells !== []) {
                $rows[] = $cells;
            }
        });

        return collect($rows);
    }

    protected function findTableByHeaders(array $needles): ?Crawler
    {
        foreach ($this->crawler->filter('table') as $node) {
            $table = new Crawler($node);
            $headers = $this->tableHeaders($table);
            foreach ($needles as $needle) {
                if (in_array($needle, $headers, true)) {
                    return $table;
                }
            }
        }

        return null;
    }
}
