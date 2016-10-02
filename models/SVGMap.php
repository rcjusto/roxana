<?php
/**
 * Created by PhpStorm.
 * User: Rogelio
 * Date: 10/17/2015
 * Time: 5:52 PM
 */

namespace app\models;


use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Yii;

class SVGMap
{

    /** @var DOMDocument $doc */
    public $doc;

    public $minValue = 0;
    public $maxValue = 100;
    public $viewBox = [
        'x' => 145,
        'y' => 312,
        'w' => 350,
        'h' => 350,
    ];

    /**
     * SVGMap constructor.
     * @param null $fileName
     */
    public function __construct($fileName = null)
    {
        if (empty($fileName)) {
            $fileName = 'worldHigh.svg';
        }

        $this->doc = new DOMDocument();
        $file = Yii::getAlias('@runtime/svg/'.$fileName);
        $this->loadFile($file);
    }

    public function loadFile($fileName)
    {
        $fileContents = file_get_contents($fileName);
        $this->doc->preserveWhiteSpace = false;
        $this->doc->loadXML($fileContents);
    }

    public function setViewBox() {
        $this->doc->documentElement->setAttribute('viewBox',implode(' ', $this->viewBox));
    }

    public function addZone($id) {
        $data = $this->getZone($id);
        if (!empty($data)) {
            $allTags = $this->doc->getElementsByTagName("g");
            foreach ($allTags as $aTag) {
                $attid = $aTag->getAttribute('id');
                if (!is_null($attid) && $attid=='zones') {
                    $elem = $this->doc->createElement('path');
                    $elem->setAttribute('id', $data['id']);
                    $elem->setAttribute('title', $data['title']);
                    $elem->setAttribute('class', $data['class']);
                    $elem->setAttribute('d', $data['d']);
                    $aTag->appendChild($elem);
                }
            }
        }
    }

    public function getZone($id) {

        $zfile = Yii::getAlias('@runtime/svg/zones.svg');
        $zfileContents = file_get_contents($zfile);
        $zdoc = new DOMDocument();
        $zdoc->preserveWhiteSpace = false;
        $zdoc->loadXML($zfileContents);

        $allTags = $zdoc->getElementsByTagName("path");
        foreach ($allTags as $aTag) {
            $attid = $aTag->getAttribute('id');
            if (!is_null($attid) && $attid==$id) {
                return [
                    'id' => $aTag->getAttribute('id'),
                    'title' => $aTag->getAttribute('title'),
                    'class' => $aTag->getAttribute('class'),
                    'd' => $aTag->getAttribute('d'),
                ];
            }
        }
        return [];
    }

    public function setBackgroundColor($color) {
        $allTags = $this->doc->getElementsByTagName("rect");
        foreach ($allTags as $aTag) {
            $attid = $aTag->getAttribute('id');
            if (!is_null($attid) && $attid=='background') {
                $aTag->setAttribute('fill', $color);
            }
        }
    }

    public function setCountryColor($color, $borderColor = null, $borderWidth = null, $id = null)
    {
        $allTags = $this->doc->getElementsByTagName("path");
        foreach ($allTags as $aTag) {
            if (is_null($id)) {
                $aTag->setAttribute('fill', $color);
                if (!is_null($borderColor)) $aTag->setAttribute('stroke', $borderColor);
                if (!is_null($borderWidth)) $aTag->setAttribute('stroke-width', $borderWidth);
            } else {
                $attid = $aTag->getAttribute('id');
                if (!is_null($attid) && $attid==$id) {
                    $aTag->setAttribute('fill', $color);
                    if (!is_null($borderColor)) $aTag->setAttribute('stroke', $borderColor);
                    if (!is_null($borderWidth)) $aTag->setAttribute('stroke-width', $borderWidth);
                }
            }
        }
    }

    public function addLegend($list, $colors) {
        $xPath = new DOMXPath($this->doc);
        $root = $xPath->query("//*[@id='legend']")->item(0);

        $dy = 10;
        $width = 100;
        $height = $dy * count($list) + 7;
        $x = $this->viewBox['x'] + $this->viewBox['w'] - $width ;
        $y = $this->viewBox['y'] + $this->viewBox['h'] - $height;

        $bck = $this->doc->createElement('rect');
        $bck->setAttribute('fill', '#ffffff');
        $bck->setAttribute('x', $x - 7);
        $bck->setAttribute('y', $y - 7);
        $bck->setAttribute('width', $width);
        $bck->setAttribute('height', $dy * count($list) + 7);
        $root->appendChild($bck);

        foreach($list as $i => $row) {
            $id = $row['option_id'];
            $name = $row['name'];
            $c = $row['c'];
            $color = $colors[$id];
            $perc = number_format($row['percent'],0) . '%';

            $g = $this->doc->createElement('g');
            $g->setAttribute('transform', "translate($x $y)");

            if ($i>0) {
                $line = $this->doc->createElement('line');
                $line->setAttribute('x1', -3);
                $line->setAttribute('y1', 0 - $dy/2 );
                $line->setAttribute('x2', $width - 11);
                $line->setAttribute('y2', 0 - $dy/2);
                $line->setAttribute('stroke-width', 0.1);
                $line->setAttribute('stroke', '#cccccc');
                $g->appendChild($line);
            }

            $circ = $this->doc->createElement('circle');
            $circ->setAttribute('cx', 0);
            $circ->setAttribute('cy', 0.2);
            $circ->setAttribute('r', 3);
            $circ->setAttribute('fill', $color);
            $g->appendChild($circ);

            $this->addText($g, $name, 5, 2, 'start');
            $this->addText($g, $c, $width - 28, 2, 'end');
            $this->addText($g, $perc, $width - 12, 2, 'end');

            $root->appendChild($g);
            $y += $dy;
        }

    }

    /**
     * @param $parent DOMElement
     * @param $text
     * @param $x
     * @param $y
     * @param $textAnchor
     */
    private function addText($parent, $text, $x, $y, $textAnchor) {
        $text2 = $this->doc->createElement('text');
        $text2->setAttribute('x', $x);
        $text2->setAttribute('y', $y);
        $text2->setAttribute('font-family', 'sans-serif');
        $text2->setAttribute('font-size', 5);
        $text2->setAttribute('text-anchor', $textAnchor);
        $text2->setAttribute('fill', '#333333');
        $tspan = $this->doc->createElement('tspan', $text);
        $text2->appendChild($tspan);
        $parent->appendChild($text2);
    }

    public function getContent()
    {
        return $this->doc->saveXML($this->doc);
    }


}