<?php

namespace app\controllers\frontend;

use Yii;
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
        $task_list = $model->getTaskWithProjectText();
        $task_dict = [];
        foreach ($task_list as $id => $one) {
            $text          = $one["project_text"]."——".$one["text"];
            $task_dict[] = [
                "key" => $id,
                "label" => $text
            ];
        }

        return $this->render('index', [
            "taskDict"         => json_encode($task_dict),
        ]);
    }
}
