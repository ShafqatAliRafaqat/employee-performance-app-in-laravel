<?php

namespace App\Helpers;


class QB
{

    public static function where($input, $param, $qb) {

        if (self::applyFilter($input, $param)) {
            $qb->where($param, $input[$param]);
        }

        return $qb;
    }

    public static function whereLike($input, $param, $qb)
    {

        if (self::applyFilter($input, $param)) {
            $qb = $qb->where($param, "LIKE", "%{$input[$param]}%");
        }

        return $qb;
    }

    public static function whereBetween($input, $param, $qb)
    {
        if (self::applyFilter($input, $param, $qb)) {
            $range = ArrayHelper::getStartAndEnd($input[$param]);
            $qb = $qb->whereBetween($param, $range);
        }

        return $qb;
    }

    private static function applyFilter($input, $param)
    {
        if (isset($input[$param])) {
            if ($input[$param] != "all") {

                if ($input[$param] == '0') {
                    return true;
                }

                if (!empty($input[$param])) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function hasWhere($model,$qb, $field = null, $operator = null, $value = null){

        $qb = $qb->whereHas($model, function ($q) use ($field, $operator, $value) {
            if ($value || $value === '0') {
                $q->where($field, $operator, $value);
            }

        });

        return $qb;
    }

    public static function whereHasIn($model,$qb, $field = null, $value = null){

        $qb = $qb->whereHas($model, function ($q) use ($field, $value) {
            if ($value) {
                $q->whereIn($field, $value);
            }

        });

        return $qb;
    }
}