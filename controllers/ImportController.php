<?php

namespace app\controllers;

use app\models\Answers;
use app\models\Countries;
use app\models\ImportForm;
use app\models\Interviews;
use app\models\Options;
use app\models\OriginalAnswers;
use app\models\Questions;
use Yii;
use yii\web\UploadedFile;

class ImportController extends BaseController
{

    public function actionIndex()
    {
        /** @var ImportForm $model */
        $model = new ImportForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            $fileName = tempnam(Yii::getAlias('@runtime/tmp/'), 'imp');
            if ($model->upload($fileName)) {
                // process file
                if (($handle = fopen($fileName, 'r')) !== FALSE) {
                    while (($row = fgetcsv($handle, 10000)) !== FALSE) {
                        if (!empty($row[0]) && trim($row[0])!='ID') {
                            $int = $this->importRow($row);
                            if (!is_null($int)) {
                                if (!$int->save()) {
                                    print_r($int->getFirstErrors());
                                    exit;
                                }
                            }
                        }
                    }
                    fclose($handle);
                }
                return $this->redirect(['interviews/index']);
            }
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionAnswers()
    {
        /** @var ImportForm $model */
        $model = new ImportForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            $fileName = tempnam(Yii::getAlias('@runtime/tmp/'), 'imp');
            if ($model->upload($fileName)) {
                // process file
                if (($handle = fopen($fileName, 'r')) !== FALSE) {
                    while (($row = fgetcsv($handle, 100000)) !== FALSE) {
                        if (!empty($row[0]) && trim($row[0])!='ID') {
                            $int = $this->getInterview($row[0]);
                            if (!is_null($int)) {
                                for($col=1; $col<count($row); $col++) {
                                    $val = $int->getOriginalAnswer($col);
                                    if (empty($val)) $int->setOriginalAnswer($col, utf8_encode($row[$col]));
                                }
                            }
                        }
                    }
                    fclose($handle);
                }
                return $this->redirect(['interviews/index']);
            }
        }

        return $this->render('index', ['model' => $model]);
    }

    /**
     * @param $row array
     * @param bool $force
     * @return Interviews|null
     */
    public function importRow($row, $force = false)
    {
        if (isset($row[0]) && !empty($row[0])) {
            $model = $this->getInterview($row[0]);
            if (!is_null($model)) {
                $model->id = $row[0];
                if (empty($model->country) || $force) $model->country = $this->parseCountry($row[0]);
                if (empty($model->from_capital) || $force) $model->from_capital = isset($row[1]) ? $this->parseFromCapital($row[1]) : null;
                if (empty($model->years_in_capital) || $force) $model->years_in_capital = isset($row[2]) ? $this->parseYearsInCapital($row[2]) : null;
                if (empty($model->city) || $force) $model->city = isset($row[3]) ? utf8_encode(trim($row[3])) : null;
                if (empty($model->zone) || $force) $model->zone = isset($row[4]) ? utf8_encode(trim($row[4])) : null;
                if (empty($model->occupation) || $force) $model->occupation = isset($row[5]) ? utf8_encode(trim($row[5])) : null;
                if (empty($model->sex) || $force) $model->sex = isset($row[6]) ? $this->parseSex($row[6]) : null;
                if (empty($model->age) || $force) $model->age = isset($row[7]) ? $row[7] : null;
                if (empty($model->education) || $force) $model->education = isset($row[8]) ? $this->parseEducation($row[8]) : null;
                if (empty($model->visited) || $force) $model->visited = isset($row[9]) ? $this->parseVisited($row[9]) : null;
                if (empty($model->lived) || $force) $model->lived = isset($row[10]) ? $this->parseVisited($row[10]) : null;
            }
            return $model;
        }
        return null;
    }

    private function parseCountry($value)
    {
        if (!empty($value)) {
            $code = substr($value, 0 , 2);
            $c = Countries::findOne($code);
            if (is_null($c)) {
                $c = new Countries();
                $c->id = $code;
                $c->name = $code;
                $c->save();
            }
            return $code;
        }
        return null;
    }

    private function parseFromCapital($value)
    {
        if (!empty($value)) {
            if (strtolower($value[0]) == 's' || strtolower($value[0]) == 'y') return 1;
            else if (strtolower($value[0]) == 'n') return 2;
        }
        return null;
    }

    private function parseYearsInCapital($value)
    {
        if (!empty($value)) {
            if (is_numeric($value)) return intval($value);
            else if (strtolower($value[0]) == 't') return 0;
        }
        return null;
    }

    private function parseSex($value)
    {
        if (!empty($value)) {
            return (strtolower($value[0]) == 'f') ? 'F' : 'M';
        }
        return null;
    }

    private function parseEducation($value)
    {
        if (!empty($value)) {
            if (strtolower($value[0]) == 'm') return 'M';
            else if (strtolower($value[0]) == 'b') return 'B';
            else if (strtolower($value[0]) == 'a') return 'A';
        }
        return null;
    }

    private function normalizeAnswer($value)
    {
        if (!empty($value)) {
            $arr = explode("\n", $value);
            if (count($arr)>1) {
                $value = implode(", ", $arr);
            }
            $arr = explode("\r", $value);
            if (count($arr)>1) {
                $value = implode(", ", $arr);
            }
            $arr = explode(";", $value);
            if (count($arr)>1) {
                $value = implode(", ", $arr);
            }
            return utf8_encode($value);
        }
        return null;
    }

    /**
     * @param $string
     * @return null|Interviews
     */
    private function getInterview($string)
    {
        return Interviews::findOne($string);
    }

    private function parseVisited($string)
    {
        return (!empty($string) && strtolower($string)!='ninguno') ? utf8_encode($string) : '';
    }

    public function actionCopyAnswer() {

        $options = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            'Totalmente de acuerdo' => '1',
            'De acuerdo' => '2',
            'Más o menos de acuerdo' => '3',
            'En desacuerdo' => '4',
            'Totalmente en desacuerdo' => '5',

        ];

        /** @var Questions[] $questions */
        $questions = Questions::find()->where('id>=20')->all();
        foreach($questions as $question) {
            echo "<h2>QUESTION " . $question->code . "</h2>";
            /** @var OriginalAnswers[] $originalAnswers */
            $originalAnswers = OriginalAnswers::find()->where(['question_id'=>$question->id])->all();
            foreach($originalAnswers as $oa) {
                echo "<h4>Interview " . $oa->interview_id . "</h4>";

                if (!empty($oa->answer) && !Answers::find()->where(['question_id'=>$oa->question_id, 'interview_id'=>$oa->interview_id])->exists()) {
                    /** @var Options $opt */
                    $opt = Options::find()->where(['question_id'=>$oa->question_id, 'name'=>$oa->answer])->one();
                    if (is_null($opt) && array_key_exists($oa->answer, $options)) {
                        $opt = new Options();
                        $opt->question_id = $oa->question_id;
                        $opt->name = $oa->answer;
                        $opt->option_id = $options[$oa->answer];
                        $opt->save();
                    }

                    if (!is_null($opt)) {
                        $answer = new Answers();
                        $answer->question_id = $oa->question_id;
                        $answer->interview_id = $oa->interview_id;
                        $answer->option_id = $opt->option_id;
                        $answer->active = 1;
                        $answer->save();
                    } else {
                        echo "Option not found: " . $oa->answer . " -> Question: " . $oa->question_id . "<br>";
                    }
                }

            }
        }
        echo "END";
    }


}
