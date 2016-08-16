<?php

namespace app\controllers;

use app\models\Countries;
use app\models\Properties;
use app\models\Questions;
use app\models\SVGColors;
use app\models\SVGMap;
use app\models\Themes;
use kartik\mpdf\Pdf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class OverThanFilter {
    private $num;

    function __construct($num) {
        $this->num = $num;
    }

    function isOver($row) {
        return $row['c'] >= $this->num;
    }
}

class PlotController extends BaseController
{

    public function actionIndex()
    {

        $questions = ArrayHelper::map(Questions::find()->where(['map' => 1])->orderBy(['id' => SORT_ASC])->all(), 'id', 'question');
        $countries = $this->getCountries();

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $template = $this->getSelectedTemplate($question);
        $data = $this->getSVGData($question, $country, (!is_null($template)) ? $template->id : 0);

        return $this->render('index', [
            'countries' => $countries,
            'country' => $country,
            'questions' => $questions,
            'question' => $question,
            'svg' => $data[0],
            'colors' => $data[1],
            'total' => $data[2],
            'template' => $template,
        ]);
    }

    public function actionSvg()
    {

        $questions = ArrayHelper::map(Questions::find()->where(['map' => 1])->orderBy(['id' => SORT_ASC])->all(), 'id', 'question');
        $countries = $this->getCountries();

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $template = $this->getSelectedTemplate($question);
        $data = $this->getSVGData($question, $country, (!is_null($template)) ? $template->id : 0);

        /** @var Response $response */
        $response = Yii::$app->getResponse();
        $response->getHeaders()
            ->set('Pragma', 'public')
            ->set('Expires', '0')
            ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->set('Content-Transfer-Encoding', 'binary')
            ->set('Content-type', 'image/svg+xml');
        $response->format = Response::FORMAT_RAW;
        $response->content = $data[0];
        return $response->send();
    }

    public function actionImage()
    {
        $questions = ArrayHelper::map(Questions::find()->where(['map' => 1])->orderBy(['id' => SORT_ASC])->all(), 'id', 'question');
        $countries = $this->getCountries();

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $template = $this->getSelectedTemplate($question);
        $outputfile = Yii::getAlias('@runtime/tmp/'.$country.$question.'.png');
        $this->generateImage($question, $country, $template, $outputfile);

        /** @var Response $response */
        $response = Yii::$app->getResponse();
        if (file_exists($outputfile)) {
            $response->getHeaders()
                ->set('Pragma', 'public')
                ->set('Expires', '0')
                ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->set('Content-Transfer-Encoding', 'binary')
                ->set('Content-type', 'image/png');
            $response->format = Response::FORMAT_RAW;
            if (!is_resource($response->stream = fopen($outputfile, 'r'))) {
                throw new ServerErrorHttpException('file access failed: permission deny');
            }
            return $response->send();
        }
    }

    public function actionPdf()
    {
        $questions = ArrayHelper::map(Questions::find()->where(['map' => 1])->orderBy(['id' => SORT_ASC])->all(), 'id', 'question');
        $countries = $this->getCountries();

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $template = $this->getSelectedTemplate($question);
        $outputfile = Yii::getAlias('@runtime/tmp/'.$country.$question.'.png');
        $this->generateImage($question, $country, $template, $outputfile);

        $content = $this->renderPartial('pdf', [
            'countries' => $countries,
            'country' => $country,
            'questions' => $questions,
            'question' => $question,
            'imgfile' => $outputfile,
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_LETTER,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD,
            // your html content input
            'content' => $content,
            'marginLeft' => 10,
            'marginRight' => 10,
            'marginTop' => 10,
            'marginBottom' => 10,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',

        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();

    }

    public function getSVGData($question, $country, $theme_id = 0)
    {

        $overMedia = Properties::getQuestionMedia($question, $country);

        $list = $this->getData($question, $country, $overMedia);

        $svgColors = new SVGColors();

        $colors1 = $svgColors->getColorsBetween('#bd7305', [255, 255, 255], 8);
        $colors2 = [];

        $svgColors->setMinColor('#d3ead2');
        $svgColors->setMaxColor('#0a8a00');
        $svgColors->setMaxColor('#bd7305');
        $svgColors->setMinColor('#1d7e26');

        $color_bkg = '#F0F0F0';
        $color_sel = '#AA0000';

        /** @var Themes $theme */
        $theme = Themes::findOne($theme_id);
        if (is_null($theme)) $theme = Themes::find()->one();
        if (!is_null($theme)) {
            if (!empty($theme->color_bkg)) $color_bkg = $theme->color_bkg;
            if (!empty($theme->color_sel)) $color_sel = $theme->color_sel;
            if (!empty($theme->color_set1)) $colors1 = array_filter(explode(',', $theme->color_set1));
            if (!$overMedia && !empty($theme->color_set2)) $colors2 = array_filter(explode(',', $theme->color_set2));
        }

        $svgMap = new SVGMap();
        $svgMap->setViewBox();
        $svgMap->setBackgroundColor('#FFFFFF');

        foreach($list as $row) {
            if (strlen($row['option_id'])>2) {
                $svgMap->addZone($row['option_id']);
            }
        }

        $svgMap->setCountryColor($color_bkg, '#AAAAAA', 0.2);
        $svgMap->setCountryColor($color_sel, '#AAAAAA', 0.2, $country);

        $total = 0;
        $colors = empty($colors2) ? $svgColors->getColors1($list, $colors1) : $svgColors->getColors2($list, $colors1, $colors2);

        if (count($list) > 0) {
            $svgMap->maxValue = $list[0]['c'];
            foreach ($list as $row) {
                $id = $row['option_id'];
                $v = $row['c'];
                $svgMap->setCountryColor($colors[$id], null, null, $id);
                $total += $v;
            }
        }

        $svgMap->addLegend($list, $colors);
        return [$svgMap->getContent(), $colors, $total];
    }

    public function getData($question_id, $country, $overMedia = false)
    {
        if ($country=='resumen') {
            $sql = "SELECT options.option_id, options.name, count(1) as c FROM answers left join interviews on answers.interview_id=interviews.id  left join options on options.question_id=answers.question_id and options.option_id=answers.option_id where answers.question_id=:qid and active=1 group by options.option_id having c>0 order by c desc;";
            $list = \Yii::$app->db->createCommand($sql, [':qid' => $question_id])->queryAll();
        } else {
            $sql = "SELECT options.option_id, options.name, count(1) as c FROM answers left join interviews on answers.interview_id=interviews.id  left join options on options.question_id=answers.question_id and options.option_id=answers.option_id where answers.question_id=:qid and country=:country and active=1 group by options.option_id having c>0 order by c desc;";
            $list = \Yii::$app->db->createCommand($sql, [':qid' => $question_id, ':country' => $country])->queryAll();
        }

        if ($overMedia) {
            $total = 0;
            foreach($list as $row) $total += intval($row['c']);
            $media = (count($list)>0) ? $total / count($list) : 0;
            if ($media>0) {
                $list = array_filter($list, array(new OverThanFilter($media), 'isOver'));
            }
        }
        return $list;
    }

    public function getSelectedTemplate($question)
    {
        $templates = ArrayHelper::map(Themes::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
        $pt = Properties::getQuestionTheme($question);
        if (!empty($templates)) {
            if (isset($_REQUEST['template']) && !empty($_REQUEST['template']) && array_key_exists($_REQUEST['template'], $templates)) {
                $template_id = $_REQUEST['template'];
                Properties::setQuestionTheme($question, $template_id);
            } else if (!empty($pt) && array_key_exists($pt, $templates)) {
                $template_id = $pt;
            } else {
                $template_id = array_keys($templates)[0];
            }
            return Themes::findOne($template_id);
        } else {
            Properties::setQuestionTheme($question, 0);
            return null;
        }

    }

    public function generateImage($question, $country, $template, $outputfile) {
        $data = $this->getSVGData($question, $country, (!is_null($template)) ? $template->id : 0);
        $svg = $data[0];

        $rasterizer_jar = Yii::getAlias('@runtime/batik-1.8/batik-rasterizer-1.8.jar');

        $tempSVG_filename = Yii::getAlias('@runtime/tmp/tmp_svg');
        $tempSVG_handle = fopen($tempSVG_filename, 'w+');
        fwrite($tempSVG_handle, $svg);
        fclose($tempSVG_handle);
        $mimetype = 'image/png';
        $width = '2000';

        $command = 'java  -jar ' . $rasterizer_jar . ' -m ' . $mimetype . ' -d ' . $outputfile . ' -w ' . $width . ' ' . $tempSVG_filename;
        shell_exec($command);
        unlink($tempSVG_filename);
        return file_exists($outputfile);
    }

    public function getCountries() {
        $arr = ArrayHelper::map(Countries::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
        $arr['resumen'] = 'General';
        return $arr;
    }

}
