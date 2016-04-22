<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "interviews".
 *
 * @property string $id
 * @property string $sex
 * @property string $age
 * @property string $education
 * @property string $country
 * @property string $city
 * @property string $zone
 * @property string $occupation
 * @property integer $from_capital
 * @property integer $years_in_capital
 * @property string $lived
 * @property string $visited
 *
 * @property Countries $country0
 */
class Interviews extends ActiveRecord
{

    public static $sex_list = ['', 'F' => 'Femenino', 'M' => 'Masculino'];
    public static $age_list = ['', '20-34' => '20-34', '35-54' => '35-54', '55 o +' => '55 o +'];
    public static $edu_list = ['', 'A' => 'Alto', 'M' => 'Medio', 'B' => 'Bajo'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'country'], 'required'],
            [['from_capital', 'years_in_capital'], 'integer'],
            [['lived','visited','occupation','city','zone'], 'string'],
            [['id'], 'string', 'max' => 50],
            [['sex', 'age', 'education'], 'string', 'max' => 45],
            [['country'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sex' => 'Sex',
            'age' => 'Age',
            'education' => 'Education',
            'country' => 'Country',
            'from_capital' => 'From Capital',
            'years_in_capital' => 'Years In Capital',
            'lived' => 'Ha vivido en',
            'visited' => 'Ha visitado',
            'city' => 'Ciudad',
            'zone' => 'Barrio',
            'occupation' => 'Ocupacion',
        ];
    }

    public function getEducationDesc() {
        switch($this->education) {
            case 'A':
                return 'Alto';
            case 'M':
                return 'Medio';
            case 'B':
                return 'Bajo';
        }
        return '';
    }

    public function getFromCapitalDesc()
    {
        switch($this->from_capital) {
            case 1:
                return 'Si';
            case 2:
                return 'No';
        }
        return '';
    }

    public function getYearsInCapitalDesc()
    {
        if (!is_null($this->years_in_capital)) {
            if ($this->years_in_capital===0 && $this->from_capital==1) return 'toda la vida';
            else return $this->years_in_capital;
        }
        return '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country']);
    }



    public function deleteAnswers($question_id) {
        Answers::deleteAll(['question_id'=>$question_id, 'interview_id'=>$this->id]);
    }

    public function addAnswer($question_id, $cad)
    {
        if (!Answers::find()->where(['question_id'=>$question_id, 'interview_id'=>$this->id, 'option_id'=>$cad])->exists()) {
            $a = new Answers();
            $a->question_id = $question_id;
            $a->interview_id = $this->id;
            $a->option_id = $cad;
            $a->active = 1;
            $a->save();
        }
    }

    /**
     * @param $question_id
     * @return string
     */
    public function getOriginalAnswer($question_id)
    {
        /** @var OriginalAnswers $model */
        $model = OriginalAnswers::findOne(['question_id'=>$question_id, 'interview_id'=>$this->id]);
        return !is_null($model) ? $model->answer : '';
    }

    /**
     * @param $question_id string
     * @param $answer string
     */
    public function setOriginalAnswer($question_id, $answer) {
        $model = OriginalAnswers::findOne(['question_id'=>$question_id, 'interview_id'=>$this->id]);
        if (is_null($model)) {
            $model = new OriginalAnswers();
            $model->question_id = $question_id;
            $model->interview_id = $this->id;
        }
        $model->answer = $answer;
        $model->save();
    }

    public function hasOption($question_id, $cad)
    {
        return Answers::find()->where(['question_id'=>$question_id, 'interview_id'=>$this->id, 'option_id'=>$cad])->exists();
    }

    /**
     * @param $question_id
     * @return Answers[]
     */
    public function getActualAnswers($question_id)
    {
        return Answers::find()->where(['question_id'=>$question_id, 'interview_id'=>$this->id])->all();
    }

    public function getActualAnswersIds($question_id)
    {
        $arr = [];
        $list = $this->getActualAnswers($question_id);
        foreach($list as $a) {
            $o = $a->getOption();
            if (!is_null($o)) $arr[] = $o->option_id;
        }
        return $arr;
    }

    public function getActualAnswersDesc($question_id, $sep = ', ')
    {
        $arr = [];
        $list = $this->getActualAnswers($question_id);
        foreach($list as $a) {
            $o = $a->getOption();
            if (!is_null($o)) $arr[] = $o->name;
        }
        sort($arr);
        return implode($sep, $arr);
    }


}
