<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Task;
use app\models\Action;
use app\models\Constants;
use app\models\Config;
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
        $action_t       = Action::tableName();
        $task_t         = Task::tableName();
        $project_t      = Project::tableName();
        $result         = $model->getQuery()
            ->select("$action_t.*, $project_t.field_id")
            ->leftJoin($task_t, "$task_t.id = $action_t.task_id")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            ->asArray()->all();
        $ret = [];
        $type_dict = Config::getTypeWithParentDict(Config::TYPE_ACTION);
        foreach ($result as $one) {
            $ret["rows"][] = [
                "id" => $one['id'],
                "data" => [
                    $one['text'],
                    $one['plan_time'],
                    ArrayHelper::getValue($type_dict[$one['field_id']], $one['type_id']),
                    ArrayHelper::getValue(Action::$status_arr, $one['status']),
                    "ceshi"
                ]
            ];
        }

        return $this->directJson(json_encode($ret));
    }

    // REST接口，获取基础数据
    public function actionList($type)
    {
        $model = new Action;
        $model->user_id = $this->user_obj->id;
        $model->status = Action::$list_arr[$type];
        $query = $model->getQuery();
        $result = $query->select("id, text, type_id, task_id, plan_time")->asArray()->all();

        return $this->directJson(json_encode($result));
    }

    public function actionAdd()
    {
        try {
            $action_type = "inserted";
            $model = new Action;

            $params_conf = [
                "text"       => [null, true],
                "task_id"    => [null, true],
                "type_id"    => [null, true],
                "plan_time"  => [null, true],
                "status"     => [Action::STATUS_INIT, false],
                "start_date" => [date("Y-m-d H:i:s", time()), false],
                "end_date"   => [date("Y-m-d H:i:s", time()), false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->task_id    = $params['task_id'];
            $model->type_id    = $params['type_id'];
            $model->plan_time  = $params['plan_time'];
            $model->status     = $params['status'];
            $model->duration   = 0;
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->user_id    = $this->user_obj->id;
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "task_id"    => [null, true],
                "type_id"    => [null, true],
                "exec_time"  => [null, true],
                "status"     => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model             = $this->findModel($id, Action::class);
            $model->text       = $params['text'];
            $model->task_id    = $params['task_id'];
            $model->type_id    = $params['type_id'];
            $model->exec_time  = $params['exec_time'];
            $model->status     = $params['status'];
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id);
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
            } else if ($model->status == Action::STATUS_FINISH) {
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
            $ret         = $this->prepareResponse($action_type, $id);
            return $this->directJson($ret);
        }
    }
}
