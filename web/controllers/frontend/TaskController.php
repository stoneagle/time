<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Constants;
use app\models\Action;
use app\models\Target;
use app\models\Area;
use app\models\Error;
use app\models\Task;
use app\models\Project;
use app\models\CountRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class TaskController extends BaseController
{
    public function behaviors()
    {
        return [
            /* [ */
            /*     'class' => 'yii\filters\HttpCache', */
            /*     'only' => ['index'], */
            /*     'lastModified' => function ($action, $params) { */
            /*         return time(); */
            /*     }, */
            /* ], */
        ];
    }

    public function actionIndex()
    {
        $model = new CountRecord;
        $model->user_id = $this->user_obj->id;
        $info = $model->getOne();

        $action_left = 1;
        if (is_null($info)) {
            $action_left = 0;
        }

        $field_dict         = Area::$field_arr;

        // 获取task任务列表
        $target          = new Target;
        $target->user_id = $this->user_obj->id;
        $dict_map       = $target->getTargetEntityDict(Constants::DICT_TYPE_MAP);

        $model       = new Task;
        $task_list   = $model->getTaskWithProjectText();
        $task_id_arr = ArrayHelper::getColumn($task_list, 'id');
        $task_dict   = [];
        foreach ($task_list as $one) {
            $entity_name = $dict_map[$one["target_id"]][$one["entity_id"]];
            $text        = $one["project_text"]."-".$entity_name."-".$one["text"];

            $task_dict[$one["id"]] = [
                "task_name"   => $one["text"],
                "text"        => $text,
                "exec_num"    => $one["exec_num"],
                "action_num"  => $one["action_num"],
                "color_class" => "task_".Area::$field_en_arr[$one["field_id"]],
            ];
        }

        return $this->render('index', [
            "action_left"   => $action_left,
            "action_info"   => json_encode($info),
            "field_arr"     => json_encode(array_flip($field_dict)),
            "task_list"     => $task_dict,
            "task_list_arr" => json_encode($task_dict),
            "task_id_arr"   => json_encode($task_id_arr),
            "status_arr"    => json_encode(Action::$status_arr),
        ]);
    }

}
