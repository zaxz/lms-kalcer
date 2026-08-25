<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class KmksParser extends AbstractParser
{
    public function parse(): array
    {
        $heading = $this->heading('Kartu Mata Kuliah (KMK)', 'h4');
        $container = null;

        if ($heading !== null) {
            $node = $heading->getNode(0);
            while ($node !== null) {
                if ($node instanceof \DOMElement) {
                    $text = $node->textContent ?? '';
                    if (str_contains($text, 'NIM:') && str_contains($text, 'Program studi:')) {
                        $container = new Crawler($node);
                        break;
                    }
                }
                $node = $node->parentNode;
            }
        }

        $result = [
            'nim' => null,
            'name' => null,
            'program' => null,
            'study_level' => null,
            'academic_year' => null,
            'semester' => null,
            'total_credits' => 0,
            'courses' => [],
        ];

        if ($container !== null) {
            $text = $container->text();
            $this->extractHeaderField($text, '/NIM:\s*(.+?)\s+Nama:/', 'nim', $result);
            $this->extractHeaderField($text, '/Nama:\s*(.+?)\s+Program studi:/', 'name', $result);
            $this->extractHeaderField($text, '/Program studi:\s*(.+?)\s+Jenjang studi:/', 'program', $result);
            $this->extractHeaderField($text, '/Jenjang studi:\s*(.+?)\s+Tahun:/', 'study_level', $result);
            if (preg_match('/Tahun:\s*(.+?)\s*•\s*Semester:\s*(\w+)/', $text, $m)) {
                $result['academic_year'] = trim($m[1]);
                $result['semester'] = trim($m[2]);
            }
        }

        $table = $this->findTableByHeaders(['Kd Mata Kuliah']);
        if ($table !== null) {
            $this->tableRows($table)->each(function (array $cells) use (&$result) {
                if (count($cells) >= 5) {
                    $result['courses'][] = [
                        'course_code' => $cells[0],
                        'course_name' => $cells[1],
                        'credits' => $this->normalizeInt($cells[2]),
                        'class_code' => $cells[3],
                        'type' => $cells[4],
                    ];
                }
            });

            $table->filter('tr')->each(function (Crawler $row) use (&$result) {
                $text = $this->cleanText($row->text());
                if (str_contains($text, 'JUMLAH SKS')) {
                    $cells = [];
                    $row->filter('td,th')->each(function (Crawler $cell) use (&$cells) {
                        $cells[] = $this->cleanText($cell->text());
                    });
                    $numeric = array_filter($cells, fn ($c) => is_numeric($c));
                    if ($numeric !== []) {
                        $result['total_credits'] = $this->normalizeInt((string) reset($numeric));
                    }
                }
            });
        }

        return $result;
    }

    protected function extractHeaderField(string $text, string $pattern, string $key, array &$result): void
    {
        if (preg_match($pattern, $text, $m)) {
            $result[$key] = trim($m[1]);
        }
    }
}
