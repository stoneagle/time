<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Error;
use app\models\Task;
use app\models\CountRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class TaskController extends BaseController
{

    public function actionIndex()
    {
        $cr_t  = CountRecord::tableName();
        $t_t   = Task::tableName();
        $model = CountRecord::find()
            ->select("$cr_t.*, $t_t.text")
            ->leftJoin($t_t, "$cr_t.task_id = $t_t.id")
            ->andWhere([
                "$cr_t.status" => [
                    CountRecord::STATUS_EXEC,
                    CountRecord::STATUS_PAUSE,
                ]
            ])
            ->andWhere(["$cr_t.user_id" => $this->user_obj->id])
            ->orderBy("$cr_t.id desc")
            ->asArray()->one();
        $action_left = 1;
        if (is_null($model)) {
            $action_left = 0;
        }
        return $this->render('index', [
            "action_left" => $action_left, 
            "action_info" => json_encode($model), 
        ]);
    }

}
