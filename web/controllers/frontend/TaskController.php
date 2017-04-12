<?php

namespace app\controllers\frontend;

use Yii;
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
        $priority_dict      = Target::$priority_arr;

        // 获取task任务列表
        $model     = new Task;
        $task_list = $model->getTaskWithFieldAndPriorityList($field_dict, $priority_dict);
        return $this->render('index', [
            "action_left"     => $action_left,
            "action_info"     => json_encode($info),
            "field_arr"       => json_encode(array_flip($field_dict)),
            "task_list"       => $task_list,
            "task_id_arr"     => json_encode(array_keys($task_list)),
            "status_arr"      => json_encode(Action::$status_arr),
        ]);
    }

}
