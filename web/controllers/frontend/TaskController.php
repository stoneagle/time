<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Action;
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

        $config_model       = new Config;
        $config_model->type = Config::TYPE_FIELD;
        $field_dict         = $config_model->getTypeDict();
        $type_dict          = Config::getTypeWithParentDict(Config::TYPE_ACTION, "select2");
        $type_raw           = Config::getTypeWithParentDict(Config::TYPE_ACTION);
        $config_model->type = Config::TYPE_PRIORITY;
        $priority_dict      = $config_model->getTypeDict();


        // 获取task任务列表
        $model     = new Task;
        $task_list = $model->getTaskWithFieldAndPriorityList($field_dict, $priority_dict);
        return $this->render('index', [
            "action_left"     => $action_left,
            "action_info"     => json_encode($info),
            "field_arr"       => json_encode(array_flip($field_dict)),
            "type_arr"        => json_encode($type_dict),
            "type_raw"        => json_encode($type_raw),
            "task_list"       => $task_list,
            "task_id_arr"     => json_encode(array_keys($task_list)),
            "status_arr"      => json_encode(Action::$status_arr),
        ]);
    }

}
