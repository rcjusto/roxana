<?php
/**
 * Created by PhpStorm.
 * User: Rogelio
 * Date: 11/5/2015
 * Time: 7:05 PM
 */

namespace app\models;


class StatsUtil
{



    public static function total($data) {
        $total = 0;
        foreach($data as $el) {
            $total += $el;
        }
        return $total;
    }

    public static function media($data, $includeZero = true) {
        $total = 0;
        $count = 0;
        foreach($data as $el) if ($includeZero || $el>0) {
            $count++;
            $total += $el;
        }
        return $total/$count;
    }

    public static function mediaData($data, $includeZero = true) {
        $total = 0;
        $count = 0;
        foreach($data as $val => $el) if ($includeZero || $el>0) {
            $count += $el;
            $total += $el * $val;
        }
        return $total/$count;
    }

   public static function moda($data) {
        $sel = 0;
        $max = 0;
        foreach($data as $val => $el) {
            if ($el>$max) {
                $max = $el;
                $sel = $val;
            }
        }
        return $sel;
    }

    public static function varianza($data, $includeZero = true)
    {
        $count = 0;
        $media = self::media($data);
        $sum_d = 0;
        $sum_d2 = 0;
        foreach($data as $el) {
            if ($includeZero || $el>0) {
                $count++;
                $d = $el - $media;
                $d2 = $d * $d;

                $sum_d += $d;
                $sum_d2 += $d2;
            }
        }
        return $sum_d2 / $count;
    }

    public static function covarianza($data1, $data2)
    {
        $data1 = array_values($data1);
        $data2 = array_values($data2);

        $media1 = self::media($data1);
        $media2 = self::media($data2);
        $sum_d = 0;
        for($index=0; $index<count($data1); $index++) {
            $sum_d += ($data1[$index] - $media1) * ($data2[$index] - $media2);
        }
        return $sum_d / count($data1);
    }

    public static function frecuenciasRelativas($list)
    {
        $result = [];
        $total = 0;
        foreach($list as $el) {
            $total += $el;
        }
        foreach($list as $k=>$v) {
            $result[$k] = $v/$total;
        }
        return $result;
    }


}