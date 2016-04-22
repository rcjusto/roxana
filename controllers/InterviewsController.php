<?php

namespace app\controllers;

use app\models\Countries;
use app\models\Options;
use app\models\Questions;
use Yii;
use app\models\Interviews;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * InterviewsController implements the CRUD actions for Interviews model.
 */
class InterviewsController extends BaseController
{


    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Lists all Interviews models.
     * @return mixed
     */
    public function actionIndex()
    {
        $questions = ArrayHelper::map(Questions::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name');
        $countries = ArrayHelper::map(Countries::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name');

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $data = Interviews::find()->where(['country'=>$country])->all();

        return $this->render('index', [
            'data' => $data,
            'countries'=>$countries,
            'country'=>$country,
            'questions'=>$questions,
            'question'=>$question,
        ]);
    }

    public function actionAnswers()
    {
        $questions = ArrayHelper::map(Questions::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name');
        $countries = ArrayHelper::map(Countries::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name');

        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 0;

        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];
        if (!array_key_exists($country, $countries)) $country = array_keys($countries)[0];

        $data = Interviews::find()->where(['country'=>$country])->all();

        return $this->render('answers', [
            'data' => $data,
            'countries'=>$countries,
            'country'=>$country,
            'questions'=>$questions,
            'question'=>$question,
        ]);
    }

    /**
     * Displays a single Interviews model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $question = isset($_REQUEST['question_id']) ? $_REQUEST['question_id'] : 0;
        return $this->redirect(['index',  'country'=>$model->country, 'question'=>$question]);
    }

    /**
     * Creates a new Interviews model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Interviews();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Interviews model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $question = 0;

            if (isset($_REQUEST['answer']) && is_array($_REQUEST['answer'])) {
                foreach($_REQUEST['answer'] as $qID => $answer) {
                    if ($question<1) $question = $qID;
                    $model->setOriginalAnswer($qID, $answer);

                    // get options
                    $field = "options_$qID";
                    // update answers
                    $model->deleteAnswers($qID);
                    if (isset($_POST[$field]) && is_array($_POST[$field])) {
                        foreach($_POST[$field] as $option_id) {
                            $model->addAnswer($qID, $option_id);
                        }
                    }
                }
            }

            return $this->redirect(['view', 'question_id' => $question, 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Interviews model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionUpdateAnswer($question_id, $id) {

        $model = $this->findModel($id);
        $model->deleteAnswers($question_id);
        if (isset($_REQUEST['options']) && !empty($_REQUEST['options'])) {
            $arr = explode(',',$_REQUEST['options']);
            foreach($arr as $option_id) {
                if (!empty($option_id))
                    $model->addAnswer($question_id, $option_id);
            }
        }

    }

    /**
     * Finds the Interviews model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Interviews the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Interviews::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
