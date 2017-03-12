<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Action;
use app\models\PlanTask;
use app\models\Task;
use app\models\Constants;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class TaskApiController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // REST接口，获取基础数据
    public function actionPlan($year, $week)
    {
        $model          = new Task;
        $model->user_id = $this->user_obj->id;
        $model->del     = Constants::SOFT_DEL_NO;
        $task_t         = Task::tableName();
        $query          = $model->getPlanTask();
        $date           = $year. "-" .$week;
        $now            = date("Y-W", time());

        if (empty($week) || ($date == $now)) {
            $date = $now ;
            $query->andWhere(["OR", 
                [
                    "AND",
                    ["$task_t.progress" => 1],
                    ["week" => $date]
                ],
                [
                    "NOT",
                    ["$task_t.progress" => 1]
                ]
            ]);
        } else {
            $query->andWhere(["week" => $date]);
        }
        $result = $query->asArray()->all();
        $ret = [];
        foreach ($result as $one) {
            $ret["rows"][] = [
                "id" => $one['id'],
                "data" => [
                    is_null($one["week"]) ? 0 : 1,
                    $date,
                    $one['text'],
                    is_null($one['sum_time']) ? 0 : $one["sum_time"],
                    ($one["progress"] == 1) ? "已完成" : "未完成",
                ]
            ];
        }
        return $this->directJson(json_encode($ret));
    }

    public function actionAdd($sid)
    {
        try {
        } catch (\exception $e) {
        }
    }

    public function actionUpdate($id)
    {
        try {
        } catch (\exception $e) {
        }
        
    }

    public function actionDel($id)
    {
        try {
        } catch (\exception $e) {
        }
    }
}
