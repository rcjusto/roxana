<?php
/**
 * Created by PhpStorm.
 * User: Rogelio
 * Date: 10/18/2015
 * Time: 1:04 PM
 */

namespace app\controllers;


use yii\filters\AccessControl;
use yii\web\Controller;

class BaseController extends Controller {

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }



}