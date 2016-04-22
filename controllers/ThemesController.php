<?php

namespace app\controllers;

use app\models\SVGColors;
use app\models\Themes;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class ThemesController extends Controller
{

    public function actionIndex()
    {
        $template_id = isset($_REQUEST['template']) ? $_REQUEST['template'] : 0;
        return $this->showPage($template_id);
    }

    public function actionUpdate() {

        /** @var Themes $model */
        $model = Themes::findOne(isset($_REQUEST['template']) ? $_REQUEST['template'] : 0);
        if (!is_null($model)) {
            $model->color_bkg = isset($_REQUEST['color_bkg']) ? $_REQUEST['color_bkg'] : null;
            $model->color_sel = isset($_REQUEST['color_sel']) ? $_REQUEST['color_sel'] : null;

            $model->color_set1 = isset($_REQUEST['color_set1']) && is_array($_REQUEST['color_set1']) ? implode(',', array_filter($_REQUEST['color_set1'])) : null;
            $model->color_set2 = isset($_REQUEST['color_set2']) && is_array($_REQUEST['color_set2']) ? implode(',', array_filter($_REQUEST['color_set2'])) : null;
            $model->save();
        }

        return $this->showPage(!is_null($model) ? $model->id : 0);

    }

    public function actionDelete() {

        /** @var Themes $model */
        $model = Themes::findOne(isset($_REQUEST['template']) ? $_REQUEST['template'] : 0);
        if (!is_null($model)) {
            $model->delete();
        }

        return $this->showPage(0);

    }

    public function actionCreate() {
        $template_id = 0;
        /** @var Themes $model */
        if (isset($_REQUEST['template_name'])) {
            $model = new Themes();
            $model->name = $_REQUEST['template_name'];
            $model->color_bkg = isset($_REQUEST['color_bkg']) ? $_REQUEST['color_bkg'] : null;
            $model->color_sel = isset($_REQUEST['color_sel']) ? $_REQUEST['color_sel'] : null;

            $model->color_set1 = isset($_REQUEST['color_set1']) && is_array($_REQUEST['color_set1']) ? implode(',', array_filter($_REQUEST['color_set1'])) : null;
            $model->color_set2 = isset($_REQUEST['color_set2']) && is_array($_REQUEST['color_set2']) ? implode(',', array_filter($_REQUEST['color_set2'])) : null;
            $model->save();
            $template_id = $model->id;
        }

        return $this->showPage($template_id);

    }

    public function actionGradient() {
        $color1 = isset($_REQUEST['gradientcolor1']) ? $_REQUEST['gradientcolor1'] : '';
        $color2 = isset($_REQUEST['gradientcolor2']) ? $_REQUEST['gradientcolor2'] : '';
        if (empty($color1)) $color1 = '#FFFFFF';
        if (empty($color2)) $color2 = '#FFFFFF';
        $c1 = SVGColors::hex2rgb($color1);
        $c2 = SVGColors::hex2rgb($color2);
        $list = SVGColors::getColorsBetween($c1, $c2, 16);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $list;
    }

    public function actionColors() {

        $session = Yii::$app->session;
        if (isset($_REQUEST['show']) && $_REQUEST['show']=='1') {
            $session->set('show_colors', '1');
        } else {
            $session->remove('show_colors');
        }

        $template_id = isset($_REQUEST['template']) ? $_REQUEST['template'] : 0;
        return $this->showPage($template_id);
    }

    private function showPage($template_id) {

        $templates = ArrayHelper::map(Themes::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        if (!empty($templates) && !array_key_exists($template_id, $templates)) $template_id = array_keys($templates)[0];
        $template = Themes::findOne($template_id);

        return $this->renderPartial('index',[
            'templates'=>$templates,
            'template'=>$template,
        ]);

    }


}
