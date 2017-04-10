<?php

namespace app\controllers\frontend;

use app\models\Error;
use app\models\FieldObj;
use app\models\FieldObjEntityLink;
use app\models\AssetsInfo;
use app\models\AssetsSub;
use app\models\Action;
use app\models\Constants;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AssetsApiController extends BaseController
{

    // todo 资产图表
    public function actionChart($year, $week)
    {
    }

}
