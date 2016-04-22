<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answers".
 *
 * @property integer $question_id
 * @property string $interview_id
 * @property string $option_id
 * @property integer $active
 */
class Answers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'interview_id', 'option_id'], 'required'],
            [['question_id', 'active'], 'integer'],
            [['interview_id'], 'string', 'max' => 50],
            [['option_id'], 'string', 'max' => 45]
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
            'option_id' => 'Option ID',
            'active' => 'Active',
        ];
    }

    /**
     * @return Options
     */
    public function getOption()
    {
        return Options::findOne(['question_id' => $this->question_id, 'option_id'=>$this->option_id]);
    }

}
