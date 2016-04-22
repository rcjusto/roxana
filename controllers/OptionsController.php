<?php

namespace app\controllers;

use app\models\Interviews;
use app\models\Questions;
use Yii;
use app\models\Options;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OptionsController implements the CRUD actions for Options model.
 */
class OptionsController extends BaseController
{


    /**
     * Lists all Options models.
     * @return mixed
     */
    public function actionIndex()
    {

        $questions = ArrayHelper::map(Questions::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name');
        $question = isset($_REQUEST['question']) ? $_REQUEST['question'] : 0;
        if (!array_key_exists($question, $questions)) $question = array_keys($questions)[0];

        $data = Options::find()->where(['question_id'=>$question])->orderBy(['name'=>SORT_ASC])->all();

        return $this->render('index', [
            'data' => $data,
            'questions'=>$questions,
            'question'=>$question,
        ]);
    }

    /**
     * Displays a single Options model.
     * @param integer $question_id
     * @param string $option_id
     * @return mixed
     */
    public function actionView($question_id, $option_id)
    {
        return $this->redirect(['index', 'question'=>$question_id]);
    }

    /**
     * Creates a new Options model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Options();

        if (isset($_REQUEST['question_id']))
            $model->question_id = $_REQUEST['question_id'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'question_id' => $model->question_id, 'option_id' => $model->option_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Options model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $question_id
     * @param string $option_id
     * @return mixed
     */
    public function actionUpdate($question_id, $option_id)
    {
        $model = $this->findModel($question_id, $option_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'question_id' => $model->question_id, 'option_id' => $model->option_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Options model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $question_id
     * @param string $option_id
     * @return mixed
     */
    public function actionDelete($question_id, $option_id)
    {
        $this->findModel($question_id, $option_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionImport()
    {
        $question = 2;
        /** @var Interviews[] $interviews */
        $interviews = Interviews::find()->where(['question_id' => $question])->all();

        if (isset($_POST['relation']) && is_array($_POST['relation'])) {
            $relations = $_POST['relation'];
            foreach ($interviews as $model) {
                $model->deleteAnswers();
                $arr1 = $model->parseAnswer();
                foreach ($arr1 as $cad) {
                    if (isset($relations[$cad]) && !empty($relations[$cad])) {
                        $model->addAnswer($relations[$cad]);
                    }
                }
            }

            return $this->redirect(['interviews/answers']);

        } else {
            $arr = [];
            foreach ($interviews as $model) {
                $arr1 = $model->parseAnswer();
                foreach ($arr1 as $cad) {
                    if (!in_array($cad, $arr)) {
                        $arr[] = trim($cad);
                    }
                }
            }
            sort($arr);
            return $this->render('import', ['data'=>$arr, 'question_id'=>$question]);
        }
    }

    /**
     * Finds the Options model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $question_id
     * @param string $option_id
     * @return Options the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($question_id, $option_id)
    {
        if (($model = Options::findOne(['question_id' => $question_id, 'option_id' => $option_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
