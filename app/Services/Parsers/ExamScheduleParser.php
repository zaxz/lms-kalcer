<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ExamScheduleParser extends AbstractParser
{
    public function parse(): array
    {
        $results = ['uts' => [], 'uas' => []];
        $tables = iterator_to_array($this->crawler->filter('table')->getIterator());

        $tableIndex = 0;
        foreach ($tables as $node) {
            $table = new Crawler($node);
            $headers = $this->tableHeaders($table);
            if (in_array('Tanggal', $headers, true) && in_array('Soal', $headers, true)) {
                // Verified: first exam table is UTS, second is UAS.
                $section = $tableIndex === 0 ? 'uts' : 'uas';
                $results[$section] = $this->parseExamTable($table, $section);
                $tableIndex++;
            }
        }

        return $results;
    }

    protected function parseExamTable(Crawler $table, string $section): array
    {
        $items = [];

        $this->tableRows($table)->each(function (array $cells) use (&$items, $section) {
            if (count($cells) >= 11) {
                $items[] = [
                    'exam_type' => strtoupper($section),
                    'date' => $this->normalizeDate($cells[0]),
                    'day' => $cells[1],
                    'start_time' => $this->normalizeTime($cells[2]),
                    'end_time' => $this->normalizeTime($cells[3]),
                    'room' => $cells[4],
                    'course_code' => $cells[5],
                    'course_name' => $cells[6],
                    'class_code' => $cells[7],
                    'lecturer' => trim($cells[8], " \t\n\r\0\x0B,"),
                    'seat_number' => $this->nullIfEmpty($cells[9]),
                    'question_status' => $this->nullIfEmpty($cells[10]),
                ];
            }
        });

        return $items;
    }
}
