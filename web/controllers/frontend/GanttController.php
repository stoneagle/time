<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Error;
use yii\filters\VerbFilter;

class GanttController extends BaseController
{

    public function actionIndex()
    {
        $config_model = new Config;
        $config_model->type = Config::TYPE_FIELD;
        $field_dict = $config_model->getQuery()->select("id as key, name as label")->asArray()->all();
        $config_model->type = Config::TYPE_ACTION;
        $action_dict = $config_model->getQuery()->select("id as key, name as label")->asArray()->all();

        return $this->render('index', [
            "fieldDict" => json_encode($field_dict),
            "actionDict" => json_encode($action_dict),
        ]);
    }

}
