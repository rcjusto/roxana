<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "themes".
 *
 * @property integer $id
 * @property string $name
 * @property string $color_bkg
 * @property string $color_sel
 * @property string $color_set1
 * @property string $color_set2
 */
class Themes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'themes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'color_bkg', 'color_sel'], 'string', 'max' => 45],
            [['color_set1', 'color_set2'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'color_bkg' => 'Color Bkg',
            'color_sel' => 'Color Sel',
            'color_set1' => 'Color Set1',
            'color_set2' => 'Color Set2',
        ];
    }

    public function getColor1($i) {
        $arr = !empty($this->color_set1) ? explode(',', $this->color_set1) : [];
        return count($arr)>$i ? $arr[$i] : '';
    }

    public function getColor2($i) {
        $arr = !empty($this->color_set2) ? explode(',', $this->color_set2) : [];
        return count($arr)>$i ? $arr[$i] : '';
    }
}
