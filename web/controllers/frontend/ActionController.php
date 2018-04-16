<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Action;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ActionController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new Action();
            return $this->validModel($model, Action::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new Action();
        $model->load(Yii::$app->request->queryParams);
        $model->join_project_id = Yii::$app->request->queryParams["project_id"];

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(True),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'typeArr'      => Action::$status_arr,
        ]);
    }
}
