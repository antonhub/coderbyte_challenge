<?php

declare(strict_types=1);

$input = ['12:25PM-12:35PM', '12:05PM-12:15PM', '10:05AM-11:05AM', '1:45PM-2:15PM'];

function getLongestTimeGapInMinutes($input): ?int
{

    // 24 hour formatted time array to be able to sort the time later
    $_24hourFormatInput = [];

    array_walk($input, function (&$v, $k) use (&$_24hourFormatInput): bool {
        $times = explode('-', $v);

        // skip if the input is not valid
        if (count($times) !== 2) {
            return false;
        }

        $start = strtotime($times[0]);
        $end = strtotime($times[1]);

        // skip if the time format is not valid
        if ($start === false || $end === false) {
            return false;
        }

        // // format times to "24 hour" format
        // $start = date('G:i', $start);
        // $end = date('G:i', $end);

        // save "time slot" start and end Unix timestamps in the new multidimensional array to not parse it again
        $_24hourFormatInput[$start] = [$start, $end];

        return true;
    });

    // if less than 2 time slots than there is nothing to calculate
    if (count($_24hourFormatInput) < 2) {
        return null;
    }

    // sorting all time slots by start time in ascending order
    ksort($_24hourFormatInput);

    // var_dump($_24hourFormatInput);
    // return null;

    // "time gaps" array
    $gaps = [];

    // the end time of the previous time slot in the loop
    $previousSlotEndTime = 0;

    foreach ($_24hourFormatInput as $timeSlot) {
        if ($previousSlotEndTime === 0) {
            $previousSlotEndTime = $timeSlot[1];
            continue;
        }

        // calculating a time gap in minutes
        $gaps[] = ($timeSlot[0] - $previousSlotEndTime) / 60;

        // update the end time for the next iteration
        $previousSlotEndTime = $timeSlot[1];
    }

    // sorting time gaps by length in descending order
    rsort($gaps);

    // the longest time gap
    return $gaps[0];
}

getLongestTimeGapInMinutes($input);
