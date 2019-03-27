<?php

namespace App\Helpers;


class ArrayHelper {

    public static function array_merge($array,$array2){
        $result = [];
        foreach ($array as $key => $value){
            $result[$key] = $value;
        }
        foreach ($array2 as $key => $value){
            $result[$key] = $value;
        }
        return $result;
    }

    public static function getStartAndEnd($string){

        $ageRange = explode('-',$string);

        $size = count($ageRange);

        if($size == 0){
            return [0,0];
        }

        $start = $ageRange[0];
        $end = null;
        if($size > 1){
            $end = $ageRange[1];
        }
        return [$start,$end];
    }

}