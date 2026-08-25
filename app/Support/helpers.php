<?php

if (! function_exists('format_number')) {
    function format_number(float|int $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, ',', '.');
    }
}
