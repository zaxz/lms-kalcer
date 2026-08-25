<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class AttendanceParser extends AbstractParser
{
    public function parseSummary(): array
    {
        $courses = [];

        $this->crawler->filter('table')->each(function (Crawler $table) use (&$courses) {
            $headers = $this->tableHeaders($table);
            if (in_array('Kode MK', $headers, true) && in_array('Persentase', $headers, true)) {
                $this->tableRows($table)->each(function (array $cells) use (&$courses) {
                    if (count($cells) >= 10) {
                        $percentage = $this->normalizePercentage($cells[9]);
                        $courses[] = [
                            'course_code' => $cells[0],
                            'course_name' => $cells[1],
                            'class_code' => $cells[2],
                            'present' => $this->normalizeInt($cells[3]),
                            'sick' => $this->normalizeInt($cells[4]),
                            'permission' => $this->normalizeInt($cells[5]),
                            'absent' => $this->normalizeInt($cells[6]),
                            'student_total_attendance' => $this->normalizeInt($cells[7]),
                            'lecturer_total_meetings' => $this->normalizeInt($cells[8]),
                            'percentage' => $percentage,
                            'status' => $this->status($percentage),
                        ];
                    }
                });
            }
        });

        $percentages = array_column($courses, 'percentage');
        $average = $percentages === [] ? 0.0 : round(array_sum($percentages) / count($percentages), 1);

        return [
            'overall_average_percentage' => $average,
            'courses' => $courses,
        ];
    }

    public function parseDetail(): array
    {
        $gradingWeights = [];
        $meetings = [];

        $this->crawler->filter('table')->each(function (Crawler $table) use (&$gradingWeights, &$meetings) {
            $headers = $this->tableHeaders($table);

            if (in_array('Pertemuan', $headers, true) && in_array('Status', $headers, true)) {
                $this->tableRows($table)->each(function (array $cells) use (&$meetings) {
                    if (count($cells) >= 4) {
                        $meetings[] = [
                            'meeting_number' => $this->normalizeInt($cells[0]),
                            'date' => $this->normalizeDate($cells[1]),
                            'time' => $this->normalizeTime($cells[2]),
                            'status' => $cells[3],
                        ];
                    }
                });
                return;
            }

            // Grading weights table: component : percentage rows.
            $this->tableRows($table)->each(function (array $cells) use (&$gradingWeights) {
                if (count($cells) >= 3 && $cells[1] === ':') {
                    $component = strtoupper($cells[0]);
                    if (preg_match('/\d+/', $cells[2], $m)) {
                        $gradingWeights[] = [
                            'component' => $component,
                            'percentage' => (int) $m[0],
                        ];
                    }
                }
            });
        });

        usort($meetings, fn ($a, $b) => $a['meeting_number'] <=> $b['meeting_number']);

        return [
            'grading_weights' => $gradingWeights,
            'meetings' => $meetings,
        ];
    }

    protected function normalizePercentage(string $value): float
    {
        $normalized = str_replace(['%', ' '], '', trim($value));

        return round((float) $normalized, 1);
    }

    protected function status(float $percentage): string
    {
        return $percentage >= 85 ? 'safe' : ($percentage >= 75 ? 'warning' : 'critical');
    }
}
