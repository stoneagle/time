<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Action;
use app\models\Error;
use app\models\Task;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class PlanController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index', [
            "init_date" => date("Y-W", time()),
        ]);
    }
}
