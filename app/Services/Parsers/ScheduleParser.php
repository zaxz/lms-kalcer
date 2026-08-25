<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ScheduleParser extends AbstractParser
{
    public function parse(): array
    {
        $items = [];

        $this->crawler->filter('table')->each(function (Crawler $table) use (&$items) {
            $headers = $this->tableHeaders($table);
            if (in_array('Hari', $headers, true) && in_array('Mata Kuliah', $headers, true)) {
                $this->tableRows($table)->each(function (array $cells) use (&$items) {
                    if (count($cells) >= 10) {
                        $items[] = $this->mapRow($cells);
                    }
                });
            }
        });

        return $this->groupByDay($items);
    }

    protected function mapRow(array $cells): array
    {
        return [
            'day' => $cells[0],
            'start_time' => $this->normalizeTime($cells[1]),
            'end_time' => $this->normalizeTime($cells[2]),
            'room' => $cells[3],
            'course_code' => $cells[4],
            'course_name' => $cells[5],
            'lecturer' => trim($cells[6], " \t\n\r\0\x0B,"),
            'type' => $cells[7],
            'class_code' => $cells[8],
            'google_classroom_id' => $this->nullIfEmpty($cells[9] ?? ''),
        ];
    }

    protected function groupByDay(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $day = $item['day'];
            $grouped[$day] ??= [];
            $grouped[$day][] = $item;
        }

        return $grouped;
    }
}
