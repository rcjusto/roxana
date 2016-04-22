<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionTest()
    {

        $outputfile = Yii::getAlias('@app/vendor/mathkashi/Kashi.php');

        include_once($outputfile);

        list($usec, $sec) = explode(' ', microtime());
        mt_srand((10000000000 * (float)$usec) ^ (float)$sec);

        $data = [];
        for ($index = 0; $index < 80; $index++)
            $data['person_' . $index] = 'resp_' . mt_rand(1, 10);

        // frecuencias absolutas
        $freq_abs = array();
        foreach ($data as $person => $resp) {
            if (array_key_exists($resp, $freq_abs)) {
                $freq_abs[$resp] = $freq_abs[$resp] + 1;
            } else {
                $freq_abs[$resp] = 1;
            }
        }

        arsort($freq_abs);

        // calcular media de frecuencias
        $total = 0;
        foreach ($freq_abs as $f) $total += $f;
        $media_freq = $total / count($freq_abs);

        //calcular frecuencia relativa
        $sum_fr = 0;
        $sum_d = 0;
        $sum_d2 = 0;
        echo "<table border='1'>";
        foreach ($freq_abs as $resp => $f) {
            $fr = $f/$total;
            $d = $f - $media_freq;
            $d2 = $d * $d;

            $sum_fr += $fr;
            $sum_d += $d;
            $sum_d2 += $d2;
            echo "<tr><td>$resp</td><td>$f</td><td>$fr</td><td>$d</td><td>$d2</td></tr>";
        }
        echo "<tr><td></td><td></td><td>$sum_fr</td><td>$sum_d</td><td>$sum_d2</td></tr>";
        echo "</table>";
        $varianza = $sum_d2 / (count($freq_abs)-1);
        $desviacion_tipica = sqrt($varianza);
        echo "<p>Varianza: $varianza</p>";
        echo "<p>Desviacion Tipica: $desviacion_tipica</p>";

        print_r($freq_abs);

        /*
                $kashi = new \Kashi();
                print_r($data);
                print_r($kashi->mode($data));
        */


    }


}
