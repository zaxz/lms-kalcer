<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class DashboardParser extends AbstractParser
{
    public function parse(): array
    {
        return [
            'academic_summary' => $this->parseAcademicSummary(),
            'announcements' => $this->parseAnnouncements(),
        ];
    }

    protected function parseAcademicSummary(): array
    {
        $heading = $this->heading('Kinerja Pembelajaran', 'h3');
        if ($heading === null) {
            return $this->emptySummary();
        }

        $node = $heading->getNode(0);
        $panel = null;
        while ($node !== null) {
            if ($node instanceof \DOMElement && $node->getAttribute('class') === 'panel') {
                $panel = new Crawler($node);
                break;
            }
            $node = $node->parentNode;
        }

        if ($panel === null) {
            return $this->emptySummary();
        }

        $summary = $this->emptySummary();
        $pendingLabels = [];

        $panel->filter('table#maininfotable tr')->each(function (Crawler $row) use (&$summary, &$pendingLabels) {
            $labels = [];
            $values = [];

            $row->filter('th')->each(function (Crawler $cell) use (&$labels) {
                $labels[] = $this->cleanText($cell->text());
            });
            $row->filter('td')->each(function (Crawler $cell) use (&$values) {
                $values[] = $this->cleanText($cell->text());
            });

            if ($labels !== []) {
                $pendingLabels = $labels;
                return;
            }

            if ($values === [] || $pendingLabels === []) {
                return;
            }

            $summary = $this->applySummaryPair($summary, $pendingLabels, $values);
            $pendingLabels = [];
        });

        return $summary;
    }

    protected function parseAnnouncements(int $limit = 5): array
    {
        $heading = $this->heading('Pengumuman (Seminggu Terakhir)', 'h3');
        if ($heading === null) {
            return [];
        }

        $node = $heading->getNode(0);
        $panel = null;
        while ($node !== null) {
            if ($node instanceof \DOMElement && $node->getAttribute('class') === 'panel') {
                $panel = new Crawler($node);
                break;
            }
            $node = $node->parentNode;
        }

        if ($panel === null) {
            return [];
        }

        $announcements = [];
        $panel->filter('ul li')->each(function (Crawler $li) use (&$announcements) {
            $parsed = $this->parseAnnouncementLi($li);
            if ($parsed !== null) {
                $announcements[] = $parsed;
            }
        });

        return array_slice($announcements, 0, $limit);
    }

    public function parseAnnouncementList(): array
    {
        $announcements = [];
        // Full list page is main.php?type=news (h2 Pengumuman, ul li)
        $this->crawler->filter('#column_b ul li, .innercontent ul li')->each(function (Crawler $li) use (&$announcements) {
            $parsed = $this->parseAnnouncementLi($li);
            if ($parsed !== null) {
                $announcements[] = $parsed;
            }
        });
        // Fallback: any ul li with date • link pattern on page
        if ($announcements === []) {
            $this->crawler->filter('ul li')->each(function (Crawler $li) use (&$announcements) {
                $parsed = $this->parseAnnouncementLi($li);
                if ($parsed !== null) {
                    $announcements[] = $parsed;
                }
            });
        }
        return $announcements;
    }

    public function parseAnnouncementDetail(): array
    {
        $inner = $this->crawler->filter('#column_b .innercontent')->first();
        if ($inner->count() === 0) {
            $inner = $this->crawler->filter('.innercontent')->first();
        }
        if ($inner->count() === 0) {
            return ['title' => null, 'date' => null, 'content' => null, 'attachment' => null, 'raw_html' => null];
        }

        // Use direct children to avoid malformed nesting (</h4> bug in LMS html)
        $children = $inner->children();
        $title = null;
        $dateRaw = null;
        $idx = 0;
        $children->each(function (Crawler $child) use (&$title, &$dateRaw, &$idx) {
            if ($idx === 0) {
                $title = $this->cleanText($child->text());
            } elseif ($idx === 1) {
                $dateRaw = $this->cleanText($child->text());
            }
            $idx++;
        });
        // Fallback if children parsing fails
        if ($title === null || $title === '') {
            $firstDiv = $inner->filter('div')->eq(1);
            if ($firstDiv->count()) $title = $this->cleanText($firstDiv->text());
        }
        if ($dateRaw === null) {
            $secondDiv = $inner->filter('div')->eq(2);
            if ($secondDiv->count()) $dateRaw = $this->cleanText($secondDiv->text());
        }
        $date = $dateRaw ? $this->parseAnnouncementDate($dateRaw) : null;

        $contentNode = $inner->filter('#articlecontent')->first();
        $contentHtml = null;
        $contentText = null;
        if ($contentNode->count() > 0) {
            $contentHtml = trim($contentNode->html());
            $contentText = $this->cleanText($contentNode->text());
            if ($contentText === '') {
                $contentText = $this->cleanText(strip_tags($contentHtml));
            }
        }

        $attachment = null;
        $inner->filter('a')->each(function (Crawler $a) use (&$attachment) {
            $href = $a->attr('href') ?? '';
            if (str_contains($href, 'uploads/news') || str_contains($href, 'image_')) {
                $attachment = $href;
            }
        });

        return [
            'title' => $title,
            'date' => $date ?? $dateRaw,
            'date_raw' => $dateRaw,
            'content' => $contentText,
            'content_html' => $contentHtml,
            'attachment' => $attachment,
            'raw_html' => $contentHtml,
        ];
    }

    protected function parseAnnouncementLi(Crawler $li): ?array
    {
        $link = $li->filter('a')->first();
        if ($link->count() === 0) {
            return null;
        }
        $href = $link->attr('href') ?: null;
        $title = $this->cleanText($link->text());
        if ($title === '') {
            return null;
        }
        $text = $this->cleanText($li->text());
        $dateText = $this->cleanText(str_replace($link->text(), '', $text));
        $dateText = trim(str_replace(['•', '-', '–'], '', $dateText));
        $dateText = preg_replace('/\s+/', ' ', $dateText);
        $id = null;
        if ($href && preg_match('/[?&]id=(\d+)/', $href, $m)) {
            $id = $m[1];
        }
        return [
            'id' => $id,
            'date' => $this->parseAnnouncementDate($dateText),
            'date_raw' => trim($dateText),
            'title' => $title,
            'href' => $href,
        ];
    }

    protected function parseAnnouncementDate(string $text): ?string
    {
        if (preg_match('/^(\d{1,2})\s+([A-Za-z]{3,12})\s+(\d{4})/', $text, $m)) {
            $month = strtolower($m[2]);
            // Normalize full month names to 3-letter key
            $monthKey = substr($month, 0, 3);
            $monthNumber = [
                'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
                'mei' => 5, 'jun' => 6, 'jul' => 7, 'agu' => 8, 'agt' => 8,
                'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
            ][$monthKey] ?? null;
            // Handle full names like januari, februari, agustus etc.
            if ($monthNumber === null) {
                $fullMap = [
                    'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                    'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                    'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
                ];
                $monthNumber = $fullMap[$month] ?? null;
            }

            if ($monthNumber !== null) {
                return sprintf('%04d-%02d-%02d', (int) $m[3], $monthNumber, (int) $m[1]);
            }
        }

        return $this->normalizeDate($text);
    }

    protected function applySummaryPair(array $summary, array $labels, array $values): array
    {
        $pairs = array_combine($labels, array_pad($values, count($labels), ''));

        if (isset($pairs['Tahun Akademik'])) {
            $summary['tahun_akademik'] = $this->nullIfEmpty($pairs['Tahun Akademik']) ?? '';
        }
        if (isset($pairs['Semester'])) {
            $summary['semester'] = $this->nullIfEmpty($pairs['Semester']) ?? '';
        }
        if (isset($pairs['IPK'])) {
            $summary['ipk'] = $this->nullIfEmpty($pairs['IPK']) ?? '';
        }
        if (isset($pairs['SKS'])) {
            $summary['total_sks'] = $this->normalizeInt($pairs['SKS'] ?? '0');
        }

        return $summary;
    }

    protected function emptySummary(): array
    {
        return [
            'tahun_akademik' => '',
            'semester' => '',
            'ipk' => '',
            'total_sks' => 0,
        ];
    }
}
