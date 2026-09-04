<?php
/**
 * Valley View Radio 97.7 MHz — weekly broadcast grid.
 *
 * Transcribed verbatim from the station's "Radio Show list" schedule. This file
 * is the single source of truth: vvu_radio.php renders the grid from it, the
 * on-air detector picks "what's playing now" out of it, and
 * dev-tools/install_radio_shows.php seeds radio_programs from the same data so
 * the show cards and the timetable can never drift apart.
 *
 * Day indexes follow PHP's date('w'): 0 = Sunday ... 6 = Saturday.
 * Times are 24-hour "HH:MM" in Africa/Accra (GMT, no DST), which is the only
 * clock the station actually broadcasts on.
 *
 * A row whose end time is <= its start time (the 22:00 -> 04:30 Hymns Night
 * block) wraps past midnight; vvr_slot_matches() handles that case.
 */

if (!function_exists('vvr_schedule_rows')) {

    /**
     * @return array<int, array{start:string, end:string, shows:array<int,string>}>
     */
    function vvr_schedule_rows()
    {
        // Shorthands for the day sets that repeat across the grid.
        $all      = [0, 1, 2, 3, 4, 5, 6];
        $sun_fri  = [0, 1, 2, 3, 4, 5];
        $weekdays = [1, 2, 3, 4, 5];

        // Each row is [start, end, [ [dayList, title], [dayList, title], ... ]].
        $rows = [
            ['04:30', '06:00', [[$all, 'Alone With God / Sabbath School Studies']]],
            ['06:00', '06:30', [[$sun_fri, 'News'], [[6], 'Maranatha Hour']]],
            ['06:30', '09:30', [[[0], 'Valley Gospel Mix'], [$weekdays, 'Morning Show'], [[6], 'Maranatha Hour']]],
            ['09:30', '10:00', [[[0], 'Valley Gospel Mix'], [[6], 'Maranatha Hour']]],
            ['10:00', '10:30', [[[0], 'Sports'], [[6], 'SDA Mix']]],
            ['10:30', '11:00', [[[0], 'Sports'], [[6], 'SDA Mix']]],
            ['11:00', '11:30', [[[0], 'Sports'], [[6], 'Sermon — VVU Church']]],
            ['11:30', '12:00', [[[0], 'Sports'], [[6], 'Sermon — VVU Church']]],
            ['12:00', '12:30', [[$sun_fri, 'News'], [[6], 'Sermon — VVU Church']]],
            ['12:30', '13:00', [[[0], 'News'], [$weekdays, 'SRC News']]],
            ['13:00', '13:30', [[[0, 2, 3, 4, 5], 'Sermon (AWR)'], [[1], 'Tech Talk'], [[6], "Children's Hour"]]],
            ['13:30', '14:00', [[[1], 'Tech Talk'], [[2, 4], 'Announcement / Interview'], [[3], 'Makafu'], [[6], "Children's Hour"]]],
            ['14:00', '14:30', [[[1], 'Radio Health Talk'], [[3], 'Makafu'], [[6], "Children's Hour"]]],
            ['14:30', '15:00', [[[1], 'Radio Health Talk'], [[3], 'Makafu'], [[6], "Children's Hour"]]],
            ['15:00', '15:30', [[$weekdays, 'Drive Time'], [[6], 'Chorale Highlife']]],
            ['15:30', '16:00', [[$weekdays, 'Drive Time'], [[6], 'Chorale Highlife']]],
            ['16:00', '16:30', [[[1, 2, 3], 'Drive Time'], [[4], 'The Business Show'], [[5], 'Themsa Hour'], [[6], 'Chorale Highlife']]],
            ['16:30', '17:00', [[[1, 2, 3], 'Drive Time'], [[4], 'The Business Show'], [[5], 'Themsa Hour'], [[6], 'Chorale Highlife']]],
            ['17:00', '17:30', [[[1, 2, 3], 'Drive Time'], [[4], 'The Business Show'], [[5], 'Themsa Hour'], [[6], 'Chorale Highlife']]],
            ['17:30', '18:00', [[$weekdays, 'Vox Pop'], [[6], 'Chorale Highlife']]],
            ['18:00', '18:30', [[[0, 1, 2, 3, 4], 'News'], [[5], 'Vesper'], [[6], 'Mizpa Hour']]],
            ['18:30', '19:00', [[[5], 'Sabbath School Lessons'], [[6], 'Mizpa Hour']]],
            ['19:00', '19:30', [[[0], 'CampuzVibe'], [[5], 'Sabbath School Lessons'], [[6], 'Mizpa Hour']]],
            ['19:30', '20:00', [[[0], 'CampuzVibe'], [[5], 'Sabbath School Lessons'], [[6], 'Mizpa Hour']]],
            ['20:00', '20:30', [[[0], 'CampuzVibe'], [[5], 'Chorale Mix'], [[6], 'Classical Music']]],
            ['20:30', '21:00', [[[0], 'CampuzVibe'], [[5], 'Chorale Mix'], [[6], 'Classical Music']]],
            ['21:00', '21:30', [[[5], 'Chorale Mix'], [[6], 'Classical Music']]],
            ['21:30', '22:00', [[[5], 'Chorale Mix'], [[6], 'Classical Music']]],
            ['22:00', '04:30', [[$all, 'Hymns Night']]],
        ];

        // Flatten each row's [dayList, title] pairs into a plain day => title map
        // so a cell can be looked up by day index alone.
        $expanded = [];
        foreach ($rows as $row) {
            list($start, $end, $groups) = $row;
            $shows = [];
            foreach ($groups as $group) {
                list($days, $title) = $group;
                foreach ($days as $day) {
                    $shows[$day] = $title;
                }
            }
            ksort($shows);
            $expanded[] = ['start' => $start, 'end' => $end, 'shows' => $shows];
        }

        return $expanded;
    }
}

if (!function_exists('vvr_day_names')) {
    function vvr_day_names()
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    }
}

if (!function_exists('vvr_show_category')) {
    /**
     * Groups a show title into one of the six colour families used by the grid
     * legend. Unknown titles fall back to "music", which is what the station
     * plays between scheduled blocks anyway.
     */
    function vvr_show_category($title)
    {
        static $map = [
            'News'                                   => 'news',
            'SRC News'                               => 'news',
            'Alone With God / Sabbath School Studies' => 'devotion',
            'Maranatha Hour'                         => 'worship',
            'Sermon — VVU Church'                    => 'worship',
            'Sermon (AWR)'                           => 'worship',
            'Vesper'                                 => 'worship',
            'Sabbath School Lessons'                 => 'worship',
            'Mizpa Hour'                             => 'worship',
            'Morning Show'                           => 'talk',
            'Tech Talk'                              => 'talk',
            'Announcement / Interview'               => 'talk',
            'Radio Health Talk'                      => 'talk',
            'Makafu'                                 => 'talk',
            'Drive Time'                             => 'talk',
            'Vox Pop'                                => 'talk',
            'The Business Show'                      => 'talk',
            'Themsa Hour'                            => 'talk',
            'CampuzVibe'                             => 'youth',
            "Children's Hour"                        => 'youth',
            'Sports'                                 => 'sports',
        ];

        return isset($map[$title]) ? $map[$title] : 'music';
    }
}

if (!function_exists('vvr_minutes')) {
    /** "HH:MM" -> minutes since midnight. */
    function vvr_minutes($hhmm)
    {
        list($h, $m) = array_map('intval', explode(':', $hhmm));
        return ($h * 60) + $m;
    }
}

if (!function_exists('vvr_format_time')) {
    /** "06:30" -> "6:30 AM", "22:00" -> "10:00 PM". */
    function vvr_format_time($hhmm)
    {
        list($h, $m) = array_map('intval', explode(':', $hhmm));
        $suffix = $h >= 12 ? 'PM' : 'AM';
        $hour   = $h % 12;
        if ($hour === 0) {
            $hour = 12;
        }
        return $hour . ':' . str_pad((string) $m, 2, '0', STR_PAD_LEFT) . ' ' . $suffix;
    }
}

if (!function_exists('vvr_slot_matches')) {
    /**
     * True when $minutes falls inside the row, including the overnight block
     * whose end time is numerically before its start.
     */
    function vvr_slot_matches($row, $minutes)
    {
        $start = vvr_minutes($row['start']);
        $end   = vvr_minutes($row['end']);

        if ($end > $start) {
            return $minutes >= $start && $minutes < $end;
        }
        return $minutes >= $start || $minutes < $end;
    }
}

if (!function_exists('vvr_station_now')) {
    /**
     * Current station time. Africa/Accra is GMT year-round, so this is stable
     * regardless of where the web server itself is hosted.
     *
     * @return array{day:int, minutes:int, label:string}
     */
    function vvr_station_now()
    {
        $now = new DateTime('now', new DateTimeZone('Africa/Accra'));
        return [
            'day'     => (int) $now->format('w'),
            'minutes' => ((int) $now->format('G') * 60) + (int) $now->format('i'),
            'label'   => $now->format('l, g:i A'),
        ];
    }
}

if (!function_exists('vvr_on_air')) {
    /**
     * Resolves what is on air at a given day/minute, plus whatever airs next.
     * Gaps in the published grid are unprogrammed music, not dead air, so they
     * are reported as such rather than as an empty slot.
     *
     * @return array{current:?array, next:?array}
     */
    function vvr_on_air($day, $minutes)
    {
        $rows    = vvr_schedule_rows();
        $current = null;

        foreach ($rows as $row) {
            if (!vvr_slot_matches($row, $minutes) || !isset($row['shows'][$day])) {
                continue;
            }
            $current = [
                'title'    => $row['shows'][$day],
                'start'    => $row['start'],
                'end'      => $row['end'],
                'category' => vvr_show_category($row['shows'][$day]),
            ];
            break;
        }

        if ($current === null) {
            $current = [
                'title'    => 'Non-Stop Music',
                'start'    => null,
                'end'      => null,
                'category' => 'music',
            ];
        }

        // Walk forward through the week (at most 8 day-steps) for the next
        // programmed block that is not a continuation of what is playing now.
        $next = null;
        for ($step = 0; $step <= 7 && $next === null; $step++) {
            $probe_day = ($day + $step) % 7;
            foreach ($rows as $row) {
                if (!isset($row['shows'][$probe_day])) {
                    continue;
                }
                $start = vvr_minutes($row['start']);
                if ($step === 0 && $start <= $minutes) {
                    continue;
                }
                if ($row['shows'][$probe_day] === $current['title'] && $step === 0) {
                    continue;
                }
                $next = [
                    'title'    => $row['shows'][$probe_day],
                    'start'    => $row['start'],
                    'end'      => $row['end'],
                    'day'      => $probe_day,
                    'category' => vvr_show_category($row['shows'][$probe_day]),
                ];
                break;
            }
        }

        return ['current' => $current, 'next' => $next];
    }
}
