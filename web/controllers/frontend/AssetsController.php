<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\BusinessAssets;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AssetsController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index', [
            "init_date" => date("Y-W", time()),
        ]);
    }
}
