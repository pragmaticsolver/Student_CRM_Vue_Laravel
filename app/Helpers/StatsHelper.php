<?php

namespace App\Helpers;

use App\Teachers;
use Carbon\Carbon;
use App\SchoolOffDays;
use Illuminate\Support\Facades\DB;

class StatsHelper {

    static private function generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display)
    {
        // prepare data for chart series
        $series_data = [];

        // For all
        $resAll = collect($resAll)->mapWithKeys(function($item){
            return [$item->custon_date => $item->count];
        })->toArray();
        $all = [];
        foreach($x_axis_dates as $x_point)
        {
            $all[] = isset($resAll[$x_point]) ? $resAll[$x_point] : 0;
        }
        $series_data[] = [ 'name' => 'All', 'data' => $all ];

        // For teachers
        $temp = [];
        foreach($resTeachers as $record)
        {
            $temp[$record->teacher_id][$record->custon_date] = $record->count;
        }
        $resTeachers = $temp;

        $teachers = Teachers::all();
        foreach($teachers as $teacher)
        {
            $temp = array();
            foreach($x_axis_dates as $x_point)
            {
                $temp[] = isset($resTeachers[$teacher->id][$x_point]) ? $resTeachers[$teacher->id][$x_point] : 0;
            }
            $series_data[] = [ 'name' => $teacher->nickname, 'data' => $temp , 'color' => $teacher->color_coding];
        }

        // construct final output
        $out = [];
        $out['xaxis_categories'] = $x_axis_display;
        $out['series'] = $series_data;
        return $out;
    }

    static function getNonZeroClassByYear()
    {
        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT DATE_FORMAT(non_zero_classes.date, '%Y') as custon_date , count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT DATE_FORMAT(non_zero_classes.date, '%Y') as custon_date, non_zero_classes.teacher_id, count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date, schedules.teacher_id
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date, non_zero_classes.teacher_id";
        $resTeachers = \DB::select($sql);

        // Prepare x axis data
        $x_axis_display = $x_axis_dates = collect($resAll)->pluck('custon_date')->toArray();

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getNonZeroClassByMonth(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('M Y');
        }
        while($x_point->addMonth() <= $end);

        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT DATE_FORMAT(non_zero_classes.date, '%Y-%m-01') as custon_date , count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT DATE_FORMAT(non_zero_classes.date, '%Y-%m-01') as custon_date, non_zero_classes.teacher_id, count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date, schedules.teacher_id
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date, non_zero_classes.teacher_id
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getNonZeroClassByWeek(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('Y-m-d');
        }
        while($x_point->addWeek() <= $end);

        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT FROM_DAYS(TO_DAYS(non_zero_classes.date) -MOD(TO_DAYS(non_zero_classes.date) -2, 7)) as custon_date , count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT FROM_DAYS(TO_DAYS(non_zero_classes.date) -MOD(TO_DAYS(non_zero_classes.date) -2, 7)) as custon_date, non_zero_classes.teacher_id, count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date, schedules.teacher_id
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date, non_zero_classes.teacher_id
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getNonZeroClassByDay(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('Y-m-d');
        }
        while($x_point->addDay() <= $end);

        // Fetch counts from from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT non_zero_classes.date as custon_date , count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT non_zero_classes.date as custon_date, non_zero_classes.teacher_id, count(*) as count
                FROM
                    (
                        SELECT distinct yoyakus.schedule_id, yoyakus.date, schedules.teacher_id
                        FROM yoyakus
                        JOIN schedules
                        ON schedules.id = yoyakus.schedule_id
                        LEFT JOIN classes_off_days
                        ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                        WHERE yoyakus.status IN (0,1)
                        AND yoyakus.waitlist = 0
                        AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                        AND classes_off_days.id IS NULL
                        AND schedules.teacher_id IS NOT NULL
                    ) as non_zero_classes
                GROUP BY custon_date, non_zero_classes.teacher_id
                HAVING custon_date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getAttendanceByYear()
    {
        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT DATE_FORMAT(yoyakus.date, '%Y') as custon_date, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                GROUP BY custon_date";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT DATE_FORMAT(yoyakus.date, '%Y') as custon_date, schedules.teacher_id, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                GROUP BY custon_date, schedules.teacher_id";
        $resTeachers = \DB::select($sql);

        // Prepare x axis data
        $x_axis_display = $x_axis_dates = collect($resAll)->pluck('custon_date')->toArray();

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getAttendanceByMonth(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('M Y');
        }
        while($x_point->addMonth() <= $end);

        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT DATE_FORMAT(yoyakus.date, '%Y-%m-01') as custon_date, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND DATE_FORMAT(yoyakus.date, '%Y-%m-01') BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY custon_date";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT DATE_FORMAT(yoyakus.date, '%Y-%m-01') as custon_date, schedules.teacher_id, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND DATE_FORMAT(yoyakus.date, '%Y-%m-01') BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY custon_date, schedules.teacher_id";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getAttendanceByWeek(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('Y-m-d');
        }
        while($x_point->addWeek() <= $end);

        // Fetch counts from  from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT FROM_DAYS(TO_DAYS(yoyakus.date) -MOD(TO_DAYS(yoyakus.date) -2, 7)) as custon_date, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND FROM_DAYS(TO_DAYS(yoyakus.date) -MOD(TO_DAYS(yoyakus.date) -2, 7)) BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY custon_date";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT FROM_DAYS(TO_DAYS(yoyakus.date) -MOD(TO_DAYS(yoyakus.date) -2, 7)) as custon_date, schedules.teacher_id, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND FROM_DAYS(TO_DAYS(yoyakus.date) -MOD(TO_DAYS(yoyakus.date) -2, 7)) BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY custon_date, schedules.teacher_id";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }

    static function getAttendanceByDay(Carbon $start, Carbon $end)
    {
        // Prepare dates
        $end_date = (clone $end)->format('Y-m-d');
        $start_date = (clone $start)->format('Y-m-d');

        // Prepare x axis data
        $x_axis_dates = [];
        $x_axis_display = [];
        $x_point = (clone $start);
        do {
            $x_axis_dates[] = $x_point->format('Y-m-d');
            $x_axis_display[] = $x_point->format('Y-m-d');
        }
        while($x_point->addDay() <= $end);

        // Fetch counts from from db
        $schoolOffdates = SchoolOffDays::pluck('date')->toArray();
        $school_off_dates_string = implode("', '", $schoolOffdates);

        // For all
        $sql = "SELECT yoyakus.date as custon_date, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND yoyakus.date BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY yoyakus.date";
        $resAll = \DB::select($sql);

        // For each teachers
        $sql = "SELECT yoyakus.date as custon_date, schedules.teacher_id, COUNT(*) as count
                FROM yoyakus
                JOIN schedules
                ON schedules.id = yoyakus.schedule_id
                LEFT JOIN classes_off_days
                ON classes_off_days.schedule_id = yoyakus.schedule_id AND classes_off_days.date = yoyakus.date
                WHERE yoyakus.status = 1
                AND yoyakus.waitlist = 0
                AND yoyakus.date NOT IN ( '$school_off_dates_string' )
                AND classes_off_days.id IS NULL
                AND schedules.teacher_id IS NOT NULL
                AND yoyakus.date BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY yoyakus.date, schedules.teacher_id";
        $resTeachers = \DB::select($sql);

        return self::generateResponse($resAll, $resTeachers, $x_axis_dates, $x_axis_display);
    }
}
