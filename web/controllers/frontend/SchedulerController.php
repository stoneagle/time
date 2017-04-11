<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Constants;
use app\models\Action;
use app\models\Error;
use app\models\Task;
use app\models\FieldObjEntityLink;
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
        $task_obj = Task::getTaskWithProject(current($task_dict)["key"]);
        $resource_dict = FieldObjEntityLink::getResourceByObjAndEntity($task_obj["obj_id"], $task_obj["field_id"], $task_obj["entity_id"], Constants::DICT_TYPE_DHX);

        return $this->render('index', [
            "initResourceDict" => json_encode($resource_dict),
            "taskTypeDict"     => json_encode($task_type_dict),
            "taskDict"         => json_encode($task_dict),
        ]);
    }
}
