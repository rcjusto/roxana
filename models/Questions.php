<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property string $question
 * @property string $code
 * @property integer $map
 * @property integer $stats
 *
 * @property Options[] $options
 */
class Questions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question','code'], 'string'],
            [['map','stats'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Pregunta',
            'code' => 'Codigo',
            'map' => 'Generar Mapa',
            'stats' => 'Ver Estadisticas',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(Options::className(), ['question_id' => 'id']);
    }

    public function getName($sep = '. ') {
        return $this->code . $sep . $this->question;
    }
}
