<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Error;
use app\models\Task;
use app\models\Target;
use app\models\Action;
use app\models\Constants;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ActionApiController extends BaseController
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
    public function actionData($task_id)
    {
        $model          = new Action;
        $model->user_id = $this->user_obj->id;
        $model->task_id = $task_id;
        $result = $model->getQuery()->asArray()->all();

        $ret = [];
        foreach ($result as $one) {
            $ret["rows"][] = [
                "id" => $one['id'],
                "data" => [
                    $one['id'],
                    $one['text'],
                    $one['plan_time'],
                    $one['status'],
                    $one['desc'],
                    ($one['status'] == Action::STATUS_INIT) ? 0 : 1,
                ]
            ];
        }

        return $this->directJson(json_encode($ret));
    }

    // REST接口，获取基础数据
    public function actionList($type)
    {
        $model          = new Action;
        $model->user_id = $this->user_obj->id;
        if ($type == Action::LIST_END) {
            $model->start_date = date("Y-m-d", time());
        }
        $model->status  = Action::$list_arr[$type];
        $query          = $model->getQuery();
        $result         = $query->asArray()->all();

        return $this->directJson(json_encode($result));
    }

    public function actionScheduler()
    {
        $model          = new Action;
        $model->status  = Action::STATUS_END;
        $model->user_id = $this->user_obj->id;
        $result         = $model->getQuery()->asArray()->all();
        $ret["data"] = $result;
        return $this->directJson(json_encode($ret));
    }

    public function actionGetTask()
    {
        try {
            $params_conf = [
                "id" => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');
            $model = $this->findModel($params["id"], Action::class);
            return $this->packageJson([
                'task_id'   => $model->task_id,
                'plan_time' => $model->plan_time,
                'text'      => $model->text,
            ], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionAdd()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $action_type = "inserted";
            $model = new Action;

            $params_conf = [
                "text"        => [null, true],
                "task_id"     => [null, true],
                "plan_time"   => [null, true],
                "status"      => [Action::STATUS_INIT, false],
                "start_date"  => [date("Y-m-d H:i:s", time()), false],
                "end_date"    => [date("Y-m-d H:i:s", time()), false],
            ];
            $params             = $this->getParamsByConf($params_conf, 'post');
            $model->id          = Project::getMaxId();
            $model->text        = $params['text'];
            $model->task_id     = $params['task_id'];
            $model->plan_time   = $params['plan_time'];
            $model->status      = $params['status'];

            if ($model->status == Action::STATUS_END) {
                $model->duration = \DateUtil::daysBetween($model->start_date, $model->end_date) + 1;
            } else {
                $model->duration   = 1;
            }
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->user_id    = $this->user_obj->id;
            if (isset($_POST["event_pid"])) {
                $model->exec_time = \DateUtil::minuteBetween($params["start_date"], $params["end_date"]) * 60;
            }
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $model->id);
            $transaction->commit(); 
            return $this->directJson($ret);
        } catch (\exception $e) {
            $transaction->rollBack(); 
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionUpdate($id)
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "task_id"    => [null, true],
                "plan_time"  => [null, true],
                "exec_time"  => [0, false],
                "status"     => [0, true],
                "start_date" => [null, false],
                "end_date"   => [null, false],
                "event_pid"  => [null, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model             = $this->findModel($id, Action::class);
            if (!is_null($params['task_id'])) {
                $model->task_id       = $params['task_id'];
            }
            if (!is_null($params['text'])) {
                $model->text       = $params['text'];
            }
            if (!is_null($params['plan_time'])) {
                $model->plan_time    = $params['plan_time'];
            }

            if (!is_null($params["event_pid"])) {
                $model->exec_time = \DateUtil::minuteBetween($params["start_date"], $params["end_date"]) * 60;
            } else {
                $model->exec_time  = $params['exec_time'];
            }
            // 执行时，更新action开始时间
            if (($model->status == Action::STATUS_EXEC) && ($model->status != $params["status"])) {
                $model->start_date = date("Y-m-d H:i:s", time());
            } 
            $model->status     = $params['status'];

            // 拖动修改时，更新开始于结束时间
            if (!is_null($params["start_date"])) {
                $model->start_date = $params["start_date"];
            }
            if (!is_null($params["end_date"])) {
                $model->end_date = $params["end_date"];
            }
            $model->duration = \DateUtil::daysBetween($model->start_date, $model->end_date);
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $id);
            $transaction->commit(); 
            return $this->directJson($ret);
        } catch (\exception $e) {
            $transaction->rollBack(); 
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $e->getMessage());
            return $this->directJson($ret);
        }
        
    }

    public function actionDel($id)
    {
        try {
            $action_type = "deleted";
            $model       = $this->findModel($id, Action::class);
            if ($model->status == Action::STATUS_EXEC) {
                throw new \Exception("该行动正在进行中，无法删除", Error::ERR_DEL);
            } else if ($model->status == Action::STATUS_END) {
                throw new \Exception("该行动已结束，无法删除", Error::ERR_DEL);
            }

            $result      = $model->delete();
            if (!$result) {
                throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            $ret = $this->prepareResponse($action_type, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $e->getMessage());
            return $this->directJson($ret);
        }
    }
}
