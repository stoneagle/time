<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Action;
use app\models\Error;
use app\models\Task;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SchedulerController extends BaseController
{
    public function actionIndex()
    {
        $model     = new Task;

        $config_model       = new Config;
        $type_raw           = Config::getTypeWithParentDict(Config::TYPE_ACTION, "dhtml");
        $priority_dict      = Config::$priority_arr;
        $field_dict         = Config::$field_arr;

        $model     = new Task;
        $task_list = $model->getTaskWithFieldAndPriorityList($field_dict, $priority_dict);
        $task_dict = [];
        foreach ($task_list as $id => $one) {
            $task_dict[] = [
                "key" => $id,
                "label" => $one["text"]
            ];
            $task_type_arr = $type_raw[$one["field_id"]];
            $task_type_dict[$id] = $task_type_arr;
        }

        return $this->render('index', [
            "taskTypeDict" => json_encode($task_type_dict),
            "taskDict"     => json_encode($task_dict),
        ]);
    }
}
