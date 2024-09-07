<?php

const DAY_OFF_INTERVAL = 2;

function makeSchedule(int $year, int $month, int $countMonths): void
{
    $dayOffCounter = -1;
    $workDayCounter = -1;

    $makeScheduleMonth = function (int $year, int $month) use (&$dayOffCounter, &$workDayCounter): string
    {
        $schedule = "";
        $daysCount = date("t",
            (DateTime::createFromFormat('Y-m-d', $year . "-" . $month . "-01"))->getTimestamp());
        for ($day = 1; $day <= $daysCount; $day++) {
            if (isWeekend($day, $month, $year)) {
                // праздник
                $schedule .= getStyledString($day, $month, $year, true);
            } else if ($workDayCounter == -1 || $dayOffCounter >= DAY_OFF_INTERVAL) {
                // раб день
                ++$workDayCounter;
                $dayOffCounter = 0;
                $schedule .= getStyledString($day, $month, $year, false);
            } else {
                // выходной
                $dayOffCounter++;
                $workDayCounter = 0;
                $schedule .= getStyledString($day, $month, $year, true);
            }
        }
        return $schedule;
    };

    for ($step = 0; $step < $countMonths; $step++) {
        echo DateTime::createFromFormat('Y-m-d', $year . "-" . $month . "-01") -> format("Y F")
            . ": "
            . $makeScheduleMonth($year, $month)
            . PHP_EOL;
        if ($month == 12) {
            $month = 1;
            $year = $year + 1;
        } else {
            $month += 1;
        }
    }
}

function getStyledString(int $day, int $month, int $year, bool $isDayOff): string
{
    $str = DateTime::createFromFormat('Y-m-d', $year . "-" . $month . "-" . $day)->format('d(D)');
    if ($isDayOff) {
        return "\033[31m " . $str . "\033[0m";
    } else {
        return "\033[32m " . $str . "\033[0m";
    }
}

function isWeekend($day, $month, $year): bool
{
    $weekday = DateTime::createFromFormat('Y-m-d', $year . "-" . $month . "-" . $day)->format('N');
    return $weekday == 6 || $weekday == 7;
}

makeSchedule(2024, 9, 13);