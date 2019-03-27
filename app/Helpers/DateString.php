<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateString
{
    public static function getDateString($start, $end, $string)
    {
       
        $d_start = Carbon::now()->Format('Y-m-d');
        $d_end = Carbon::now()->Format('Y-m-d');

        if ($d_start == $start && $d_end == $end) {
            return "Current day $string";
        }

        $p_d_start = Carbon::now()->subday(1)->Format('Y-m-d');
        $p_d_end = Carbon::now()->subday(1)->Format('Y-m-d');

        if ($p_d_start == $start && $p_d_end == $end) {
            return "Previously day $string";
        }

        $w_start = Carbon::now()->startOfWeek()->subday(1)->Format('Y-m-d');
        $w_end = Carbon::now()->endOfWeek()->subday()->Format('Y-m-d');

        if ($w_start == $start && $w_end == $end) {
            return "Current Week $string";
        }

        $p_w_start = Carbon::now()->startOfWeek()->subWeeks(1)->subday(1)->Format('Y-m-d');
        $p_w_end = Carbon::now()->subWeeks(1)->endOfWeek()->subday(1)->Format('Y-m-d');

        if ($p_w_start == $start && $p_w_end == $end) {
            return "Previously Week $string";
        }

        $m_start = Carbon::now()->startOfMonth()->Format('Y-m-d');
        $m_end = Carbon::now()->endOfMonth()->Format('Y-m-d');

        if ($m_start == $start && $m_end == $end) {
            return "Current Month $string";
        }
        $p_m_start = Carbon::now()->startOfMonth()->subMonth()->Format('Y-m-d');
        $p_m_end = Carbon::now()->subMonth()->endOfMonth()->Format('Y-m-d');

        if ($p_m_start == $start && $p_m_end == $end) {
            return "Previously Month $string";
        }

        $y_start = Carbon::now()->startOfYear()->Format('Y-m-d');
        $y_end = Carbon::now()->endOfYear()->Format('Y-m-d');

        if ($y_start == $start && $y_end == $end) {
            return "Current Year $string";
        }

        $p_y_start = Carbon::now()->startOfYear()->subYear()->Format('Y-m-d');
        $p_y_end = Carbon::now()->subYear()->endOfYear()->Format('Y-m-d');

        if ($p_y_start == $start && $p_y_end == $end) {
            return "Previously Year $string";
        }
        
        return "$string form $start to $end";
    }
}