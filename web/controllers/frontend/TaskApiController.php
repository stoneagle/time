<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Task;
use app\models\Constants;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class TaskApiController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // REST接口，获取基础数据
    public function actionData()
    {
    }

    public function actionAdd($sid)
    {
        try {
        } catch (\exception $e) {
        }
    }

    public function actionUpdate($id)
    {
        try {
        } catch (\exception $e) {
        }
        
    }

    public function actionDel($id)
    {
        try {
        } catch (\exception $e) {
        }
    }
}
