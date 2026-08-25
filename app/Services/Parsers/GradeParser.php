<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class GradeParser extends AbstractParser
{
    public function parseKhs(): array
    {
        $semesters = [];

        $this->crawler->filter('table')->each(function (Crawler $table) use (&$semesters) {
            $headers = $this->tableHeaders($table);
            if (! in_array('Grade', $headers, true) || ! in_array('Bobot', $headers, true)) {
                return;
            }

            $meta = $this->semesterMetaBeforeTable($table->getNode(0));
            $courses = [];
            $totals = ['total_credits' => 0, 'total_weighted_credits' => 0, 'ip_semester' => 0.0];

            $table->filter('tr')->each(function (Crawler $row) use (&$courses, &$totals) {
                $cells = [];
                $row->filter('td,th')->each(function (Crawler $cell) use (&$cells) {
                    $cells[] = $this->cleanText($cell->text());
                });

                if ($cells === []) {
                    return;
                }

                // Footer: JUMLAH (colspan 4) | SKS | weighted.
                if (in_array('JUMLAH', $cells, true) && count($cells) >= 3) {
                    $totals['total_credits'] = $this->normalizeInt($cells[1] ?? $cells[4] ?? '0');
                    $totals['total_weighted_credits'] = $this->normalizeInt($cells[2] ?? $cells[5] ?? '0');
                    return;
                }

                // Footer: IP SEMESTER | value.
                if (in_array('IP SEMESTER', $cells, true)) {
                    $value = end($cells);
                    $totals['ip_semester'] = $this->normalizeDecimal((string) $value);
                    return;
                }

                // Header row.
                if ($this->cleanText($cells[0]) === 'Mata Kuliah' || ($cells[0] ?? '') === 'Mata Kuliah') {
                    return;
                }

                if (count($cells) >= 6) {
                    $courses[] = [
                        'course_code' => $cells[0],
                        'course_name' => $cells[1],
                        'grade' => $cells[2],
                        'weight' => $this->normalizeInt($cells[3]),
                        'credits' => $this->normalizeInt($cells[4]),
                        'weighted_credits' => $this->normalizeInt($cells[5]),
                    ];
                }
            });

            $semesters[] = $meta + $totals + ['courses' => $courses];
        });

        return ['semesters' => $semesters];
    }

    public function parseCumulative(): array
    {
        $courses = [];
        $totals = ['total_credits' => 0, 'total_weighted_credits' => 0, 'ipk' => 0.0];

        $this->crawler->filter('table')->each(function (Crawler $table) use (&$courses, &$totals) {
            $headers = $this->tableHeaders($table);
            if (! in_array('Grade', $headers, true) || ! in_array('Bobot', $headers, true)) {
                return;
            }

            $table->filter('tr')->each(function (Crawler $row) use (&$courses, &$totals) {
                $cells = [];
                $row->filter('td,th')->each(function (Crawler $cell) use (&$cells) {
                    $cells[] = $this->cleanText($cell->text());
                });

                if ($cells === []) {
                    return;
                }

                if (in_array('JUMLAH', $cells, true) && count($cells) >= 3) {
                    $totals['total_credits'] = $this->normalizeInt($cells[1] ?? $cells[4] ?? '0');
                    $totals['total_weighted_credits'] = $this->normalizeInt($cells[2] ?? $cells[5] ?? '0');
                    return;
                }

                if (in_array('IPK TERAKHIR', $cells, true)) {
                    $totals['ipk'] = $this->normalizeDecimal((string) end($cells));
                    return;
                }

                if (($cells[0] ?? '') === 'Mata Kuliah') {
                    return;
                }

                if (count($cells) >= 6) {
                    $courses[] = [
                        'course_code' => $cells[0],
                        'course_name' => $cells[1],
                        'grade' => $cells[2],
                        'weight' => $this->normalizeInt($cells[3]),
                        'credits' => $this->normalizeInt($cells[4]),
                        'weighted_credits' => $this->normalizeInt($cells[5]),
                    ];
                }
            });
        });

        return $totals + ['courses' => $courses];
    }

    protected function semesterMetaBeforeTable(\DOMNode $tableNode): array
    {
        $meta = [
            'academic_year' => null,
            'semester_name' => null,
            'semester_number' => null,
        ];

        $sibling = $tableNode->previousSibling;
        $looked = 0;
        while ($sibling !== null && $looked < 8) {
            if ($sibling instanceof \DOMElement && strtoupper($sibling->tagName) === 'H5') {
                $text = $this->cleanText($sibling->textContent);
                if (preg_match('/Tahun:\s*(.+?)\s*•\s*Semester:\s*(\d+|\w+)/i', $text, $m)) {
                    if ($meta['academic_year'] === null) {
                        $meta['academic_year'] = trim($m[1]);
                    }
                    $val = trim($m[2]);
                    if (is_numeric($val)) {
                        if ($meta['semester_number'] === null) {
                            $meta['semester_number'] = (int) $val;
                        }
                    } elseif ($meta['semester_name'] === null) {
                        $meta['semester_name'] = $val;
                    }
                    // If we already have semester_number from the nearest H5, keep it and stop
                    if ($meta['semester_number'] !== null) {
                        break;
                    }
                } elseif (preg_match('/Semester:\s*(\d+)/i', $text, $m)) {
                    if ($meta['semester_number'] === null) {
                        $meta['semester_number'] = (int) $m[1];
                    }
                    // Continue to look for Tahun header a bit further, but never overwrite closest number
                    // If next sibling beyond is Tahun, the loop will capture it without overwriting.
                    // To avoid overwriting with a farther H5, we keep checking but guard with null check.
                    // If we already have number, we only need to check one more Tahun header, so we can break after finding it -
                    // the outer loop limit will handle it. For safety, if we have number and we've already seen a Tahun candidate, break.
                }
            }
            $sibling = $sibling->previousSibling;
            $looked++;
            // If we have semester_number and we've looked far enough to possibly capture Tahun (up to 4 steps past number), break after 4 extra steps
            // Instead simple: if semester_number found and academic_year found, break
            if ($meta['semester_number'] !== null && $meta['academic_year'] !== null) {
                break;
            }
        }

        return $meta;
    }
}
