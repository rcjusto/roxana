<?php
/**
 * Created by PhpStorm.
 * User: Rogelio
 * Date: 10/17/2015
 * Time: 5:52 PM
 */

namespace app\models;


use Yii;

class SVGColors
{


    public $minColor = [255, 255, 255];
    public $maxColor = [255, 0, 0];
    public $minValue = 0;
    public $maxValue = 100;

    /**
     * SVGMap constructor.
     * @param null $fileName
     */
    public function __construct($fileName = null)
    {
    }

    public function getColorForValue($value)
    {

        $r = ($value - $this->minValue) / ($this->maxValue - $this->minValue);

        $c0 = ($this->maxColor[0] - $this->minColor[0]) * $r + $this->minColor[0];
        $c1 = ($this->maxColor[1] - $this->minColor[1]) * $r + $this->minColor[1];
        $c2 = ($this->maxColor[2] - $this->minColor[2]) * $r + $this->minColor[2];
        return $this->rgb2hex([$c0, $c1, $c2]);
    }


    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function rgb2hex($rgb)
    {
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex; // returns the hex value including the number sign (#)
    }

    /**
     * @param array $minColor
     */
    public function setMinColor($minColor)
    {
        $this->minColor = $this->hex2rgb($minColor);
    }

    /**
     * @param array $maxColor
     */
    public function setMaxColor($maxColor)
    {
        $this->maxColor = $this->hex2rgb($maxColor);;
    }


    public function getMedia($list)
    {
        $total = 0;
        foreach ($list as $row) {
            $total += $row['c'];
        }
        return count($list) > 0 ? $total / count($list) : 0;
    }

    public function getColors2($list, $colors, $colors1)
    {
        $result = array();

        // get min and max
        $values = [];
        $values1 = [];
        $values2 = [];
        $media = $this->getMedia($list);
        $this->minValue = 999999999;
        $this->maxValue = 0;
        $index = 0;
        $index1 = 0;
        $index2 = 0;
        foreach ($list as $row) {
            if ($row['c'] < $this->minValue) $this->minValue = $row['c'];
            if ($row['c'] > $this->maxValue) $this->maxValue = $row['c'];
            if (!array_key_exists($row['c'], $values)) $values[$row['c']] = $index++;
            if ($row['c'] >= $media) {
                if (!array_key_exists($row['c'], $values1)) $values1[$row['c']] = $index1++;
            } else {
                if (!array_key_exists($row['c'], $values2)) $values2[$row['c']] = $index2++;
            }
        }

        if (count($values1)*2<=count($colors)) {
            foreach($values1 as $c => $index) $values1[$c] = 2 * $index;
        }
        if (count($values2)*2<=count($colors1)) {
            foreach($values2 as $c => $index) $values2[$c] = 2 * $index;
        }

        // calculate colors
        foreach ($list as $row) {

            if ($row['c'] >= $media) {
                $index = $values1[$row['c']];
                $result[$row['option_id']] = (isset($colors[$index])) ? $colors[$index] : $colors[count($colors) - 1];
            } else {
                $index = $values2[$row['c']];
                $result[$row['option_id']] = (isset($colors1[$index])) ? $colors1[$index] : $colors1[count($colors1) - 1];
            }
        }

        return $result;
    }

    public function getColors1($list, $colors)
    {
        $result = array();

        // get min and max
        $values = [];
        $this->minValue = 999999999;
        $this->maxValue = 0;
        $index = 0;
        foreach ($list as $row) {
            if ($row['c'] < $this->minValue) $this->minValue = $row['c'];
            if ($row['c'] > $this->maxValue) $this->maxValue = $row['c'];
            if (!array_key_exists($row['c'], $values)) $values[$row['c']] = $index++;
        }

        if (count($values)*4-1<=count($colors)) {
            foreach($values as $c => $index) $values[$c] = 4 * $index;
        } if (count($values)*3-1<=count($colors)) {
            foreach($values as $c => $index) $values[$c] = 3 * $index;
        } else if (count($values)*2-1<=count($colors)) {
            foreach($values as $c => $index) $values[$c] = 2 * $index;
        }

        // calculate colors
        foreach ($list as $row) {
            $index = $values[$row['c']];
            $result[$row['option_id']] = (isset($colors[$index])) ? $colors[$index] : $colors[count($colors) - 1];
        }

        return $result;
    }


    public static function getColorsBetween($c1, $c2, $q)
    {
        $result = [];
        for ($i = 0; $i < $q; $i++) {
            $r = ($q - $i) / ($q);
            $cr = ($c1[0] - $c2[0]) * $r + $c2[0];
            $cg = ($c1[1] - $c2[1]) * $r + $c2[1];
            $cb = ($c1[2] - $c2[2]) * $r + $c2[2];
            $result[] = self::rgb2hex([$cr, $cg, $cb]);
        }
        return $result;
    }

}