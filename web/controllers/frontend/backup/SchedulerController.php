<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Process;
use app\models\Config;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SchedulerController extends BaseController
{

    public function actionIndex()
    {
        $model          = new Process;
        $model->user_id = $this->user_obj->id;
        $process_dict   = $model->getQuery()->select("id as key, text as label")->asArray()->all();

        $config_model       = new Config;
        $config_model->type = Config::TYPE_ACTION;
        $action_dict        = $config_model->getTypeDict();

        return $this->render('index', [
            "processDict" => json_encode($process_dict),
            "actionDict"  => $action_dict,
        ]);
    }

}
