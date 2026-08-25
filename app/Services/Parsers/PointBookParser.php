<?php

namespace App\Services\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class PointBookParser extends AbstractParser
{
    public function parse(): array
    {
        $entries = [];

        $table = $this->findTableByHeaders(['Tanggal', 'Nama Kegiatan']);
        if ($table !== null) {
            $this->tableRows($table)->each(function (array $cells) use (&$entries) {
                if (count($cells) >= 4) {
                    $entries[] = [
                        'date' => $this->normalizeDate($cells[0]),
                        'activity_name' => $cells[1],
                        'points' => $this->normalizeInt($cells[2]),
                        'note' => $cells[3],
                    ];
                }
            });
        }

        return [
            'entries' => $entries,
            'total_points' => array_sum(array_column($entries, 'points')),
        ];
    }
}
