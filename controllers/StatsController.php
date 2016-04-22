<?php

namespace app\controllers;

use app\models\Countries;
use app\models\Interviews;
use app\models\Questions;
use yii\helpers\ArrayHelper;

class StatsController extends BaseController
{
    public function actionIndex()
    {

        $questions = ArrayHelper::map(Questions::find()->where(['stats' => 1])->orderBy(['id' => SORT_ASC])->all(), 'id', 'name');
        $countries = ArrayHelper::map(Countries::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $template = $this->getTemplateForQuestion($question);

        $data = ['all' => $this->getData($question, $country), 'real'=> $this->getRealData($question, $country)];

        foreach(Interviews::$sex_list as $key => $value)
            if (!empty($value)) {
                $data[$value] = $this->getData($question, $country, "interviews.sex='$key'");
                $data['real-'.$value] = $this->getRealData($question, $country, "interviews.sex='$key'");
            }

        foreach(Interviews::$age_list as $key => $value)
            if (!empty($value)) {
                $data[$value] = $this->getData($question, $country, "interviews.age='$key'");
                $data['real-'.$value] = $this->getRealData($question, $country, "interviews.age='$key'");
            }

        foreach(Interviews::$edu_list as $key => $value)
            if (!empty($value)) {
                $data[$value] = $this->getData($question, $country, "interviews.education='$key'");
                $data['real-'.$value] = $this->getRealData($question, $country, "interviews.education='$key'");
            }

        return $this->render($template, [
            'countries' => $countries,
            'country' => $country,
            'questions' => $questions,
            'question' => $question,
            'data' => $data,
        ]);

    }

    private function getTemplateForQuestion($question)
    {
        if ($question>=20) return 'index3';
        return 'index2';
    }

    public function getData($question_id, $country, $extraWhere = '1=1') {
        $sql = "SELECT options.name , count(1) as c
                FROM answers left join interviews on answers.interview_id=interviews.id  left join options on options.question_id=answers.question_id and options.option_id=answers.option_id
                where answers.question_id=:qid and country=:country and active=1 and $extraWhere
                group by options.name";

        $result = [];
        $list = \Yii::$app->db->createCommand($sql, [':qid'=>$question_id,':country'=>$country])->queryAll();
        foreach($list as $row) {
            $result[$row['name']] = $row['c'];
        }
        return $result;
    }

    public function getRealData($question_id, $country, $extraWhere = '1=1') {
        $sql = "SELECT answers.option_id , count(1) as c
                FROM answers left join interviews on answers.interview_id=interviews.id
                where answers.question_id=:qid and country=:country and active=1 and $extraWhere
                group by answers.option_id";

        $result = [];
        $list = \Yii::$app->db->createCommand($sql, [':qid'=>$question_id,':country'=>$country])->queryAll();
        foreach($list as $row) {
            $result[$row['option_id']] = $row['c'];
        }
        return $result;
    }

}
