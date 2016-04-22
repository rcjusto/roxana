<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "original_answers".
 *
 * @property integer $question_id
 * @property string $interview_id
 * @property string $answer
 */
class OriginalAnswers extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'original_answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'interview_id'], 'required'],
            [['question_id'], 'integer'],
            [['answer'], 'string'],
            [['interview_id'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'interview_id' => 'Interview ID',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Questions::className(), ['id' => 'question_id']);
    }

    public function parseAnswer() {
        $arr = [];
        if (!empty($this->answer)) {
            $arr1 = preg_split('/[&,\/]+/',$this->answer);
            foreach($arr1 as $cad) {
                $arr2 = explode(' y ', $cad);
                foreach($arr2 as $cad2) {
                    $cad2 = strtolower(trim($cad2));
                    $cad2 = iconv('UTF-8','ASCII//TRANSLIT',$cad2);
                    $cad2 = str_replace(['.',"'",'~','`'],'',$cad2);
                    if (!empty($cad2) && !in_array($cad2, $arr)) {
                        $arr[] = trim($cad2);
                    }
                }
            }
        }
        return $arr;
    }

}
