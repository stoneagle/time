<?php

namespace app\controllers\frontend;

use Yii;
use app\models\GanttTasks;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SchedulerController extends BaseController
{

    public function actionIndex()
    {
        $model          = new GanttTasks;
        $model->user_id = $this->user_obj->id;
        $model->type    = GanttTasks::LEVEL_TASK;
        $task_dict      = $model->getQuery()->select("id as key, text as label")->asArray()->all();

        return $this->render('index', [
            "taskDict" => json_encode($task_dict),
        ]);
    }

}
