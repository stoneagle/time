<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Constants;
use app\models\Action;
use app\models\Target;
use app\models\Error;
use app\models\Task;
use app\models\Area;
use app\models\FieldObjEntityLink;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class SchedulerController extends BaseController
{
    public function actionIndex()
    {
        $target          = new Target;
        $target->user_id = $this->user_obj->id;
        $dict_map       = $target->getTargetEntityDict(Constants::DICT_TYPE_MAP);

        $model     = new Task;
        $task_list = $model->getTaskWithProjectText();
        $task_dict = [];
        foreach ($task_list as $one) {
            $entity_name = $dict_map[$one["target_id"]][$one["entity_id"]];
            $text          = $one["project_text"]."-".$entity_name."-".$one["text"];
            $task_dict[] = [
                "key"   => $one["id"],
                "label" => $text
            ];
        }

        return $this->render('index', [
            "fieldDict" => json_encode(Area::$field_en_arr),
            "taskDict"  => json_encode($task_dict),
        ]);
    }
}
