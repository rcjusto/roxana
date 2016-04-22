<?php
/**
 * Created by PhpStorm.
 * User: Rogelio
 * Date: 10/17/2015
 * Time: 10:26 AM
 */

namespace app\models;


use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{

    /** @var  $file UploadedFile */
    public $file;
    public $question_id;

    public function rules()
    {
        return [
            [['question_id'], 'safe'],
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function upload($tmp_name)
    {
        if ($this->validate()) {
            $this->file->saveAs($tmp_name);
            return true;
        } else {
            return false;
        }
    }

}