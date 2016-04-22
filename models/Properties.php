<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "properties".
 *
 * @property string $id
 * @property string $value
 */
class Properties extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'properties';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'string', 'max' => 45],
            [['value'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
        ];
    }


    public static function getQuestionTheme($question_id) {
        $id = 'question_theme_' . $question_id;
        $model = Properties::findOne($id);
        return !is_null($model) ? $model->value : null;
    }

    public static function setQuestionTheme($question_id, $theme_id) {
        $id = 'question_theme_' . $question_id;
        $model = Properties::findOne($id);
        if (is_null($model)) {
            $model = new Properties();
            $model->id = $id;
        }
        $model->value = $theme_id;
        $model->save();
    }

    public static function getQuestionMedia($question_id, $country) {
        $id = 'question_media_' . $country;
        $model = Properties::findOne($id);
        return !is_null($model) ? $model->value : null;
    }

    public static function setQuestionMedia($question_id, $country, $over_media) {
        $id = 'question_media_' . $country;
        $model = Properties::findOne($id);
        if (is_null($model)) {
            $model = new Properties();
            $model->id = $id;
        }
        $model->value = $over_media;
        $model->save();
    }

}
