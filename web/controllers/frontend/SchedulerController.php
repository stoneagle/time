<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Error;
use yii\filters\VerbFilter;

class SchedulerController extends BaseController
{

    public function actionIndex()
    {

        return $this->render('index', [
        ]);
    }

}
