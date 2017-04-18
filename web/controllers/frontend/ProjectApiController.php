<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Task;
use app\models\Action;
use app\models\GanttLinks;
use app\models\Constants;
use app\models\Error;
use app\models\Target;
use app\models\TargetEntityLink;
use Yii;
use yii\filters\VerbFilter;

class ProjectApiController extends BaseController
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
    public function actionData()
    {
        $result = [];

        $model          = new Project;
        $model->user_id = $this->user_obj->id;
        $query          = $model->getTargetQuery();
        $data           = $query->asArray()->all();
        foreach ($data as $one) {
            $one['type'] = Project::LEVEL_PROJECT;
            $result[] = $one;
        }

        $task_model          = new Task;
        $task_model->user_id = $this->user_obj->id;
        $query               = $task_model->getTaskWithActionQuery();
        $task_data           = $query->asArray()->all();
        foreach ($task_data as $one) {
            $one['type'] = Project::LEVEL_TASK;
            $result[] = $one;
        }

        $action_model          = new Action;
        $action_t              = Action::tableName();
        $action_model->user_id = $this->user_obj->id;
        $action_data           = $action_model->getQuery()
            ->select("
                $action_t.*,
                $action_t.task_id as parent")
            ->asArray()->all();
        foreach ($action_data as $one) {
            $one["start_date"] = date("Y-m-d 00:00:00", strtotime($one["start_date"]));
            $one["end_date"] = date("Y-m-d 00:00:00", strtotime($one["end_date"]) + 60 * 60 * 24);
            if ($one["status"] == Action::STATUS_END) {
                $one["progress"] = 1;
            } else {
                $one["progress"] = 0;
            }
            $one['type'] = Project::LEVEL_ACTION;
            $result[] = $one;
        }

        foreach ($result as &$one) {
            $one["sort_date"] = $one["start_date"];
            switch ($one["type"]) {
                case Project::LEVEL_PROJECT :
                case Project::LEVEL_TASK :
                    $one['duration'] = "";
                    $one['start_date'] = "";
                    $one['open'] = false;
                    break;
                case Project::LEVEL_ACTION :
                    break;
                default :
                    break;
            }

            // 待定项目配置
            if ($one['duration'] === 0) {
                $one['unscheduled'] = true;
            }
        }
        $ret['data']  = $result;
        $links        = GanttLinks::find()->asArray()->all();
        $ret['links'] = $links;

        return $this->directJson(json_encode($ret));
    }

    public function actionTaskAdd()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $action_type = "inserted";
            $params_conf = [
                "text"        => [null, true],
                "type"        => [null, true],
                "start_date"  => [null, true],
                "end_date"    => [null, false],
                "duration"    => [0, true],
                "progress"    => [0, false],
                "target_id"   => [0, false],
                "entity_id"   => [0, false],
                "parent"      => [null, true],
                "plan_time"   => [null, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            switch ($params['type']) {
                case Project::LEVEL_PROJECT :
                    $model              = new Project;
                    $model->id          = Project::getMaxId();
                    $model->target_id      = $params['target_id'];
                    $model->progress    = $params['progress'];
                    break;
                case Project::LEVEL_TASK :
                    // task与action的id不能跟project重复
                    $model            = new Task;
                    $model->id        = Project::getMaxId();
                    $model->parent    = (int)$params['parent'];
                    $model->progress  = $params['progress'];
                    $model->entity_id = $params['entity_id'];
                    break;
                case Project::LEVEL_ACTION :
                    $model              = new Action;
                    $model->id          = Project::getMaxId();
                    $model->task_id     = $params["parent"];
                    $model->plan_time   = $params["plan_time"];
                    $model->end_date    = $params['start_date'];
                    $model->status      = Action::STATUS_INIT;
                    break;
                default :
                    throw new \Exception("gantt类型出错", Error::ERR_GANTT_TYPE);
                    break;
            }
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            $model->duration   = $params['duration'];
            $model->user_id    = $this->user_obj->id;
            $model->modelValidSave();
            $transaction->commit(); 
            $ret = $this->prepareResponse($action_type, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $transaction->rollBack(); 
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionTaskUpdate($id)
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "type"       => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, false],
                "duration"   => [null, true],
                "progress"   => [0, false],
                "target_id"  => [0, false],
                "entity_id"  => [0, false],
                "parent"     => [null, true],
                "plan_time"  => [null, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            switch ($params['type']) {
                case Project::LEVEL_PROJECT :
                    $model            = $this->findModel($id, Project::class);
                    $model->target_id = $params['target_id'];
                    $model->progress  = $params['progress'];
                    break;
                case Project::LEVEL_TASK :
                    $model            = $this->findModel($id, Task::class);
                    $model->parent    = (int)$params['parent'];
                    $model->progress  = $params['progress'];
                    $model->entity_id = $params['entity_id'];
                    break;
                case Project::LEVEL_ACTION :
                    $model             = $this->findModel($id, Action::class);
                    $model->task_id    = $params["parent"];
                    if ($model->status != Action::STATUS_END) {
                        $model->plan_time  = $params["plan_time"];
                        $model->duration   = $params['duration'];
                        $model->start_date = $params['start_date'];
                        $model->end_date   = $params['start_date'];
                    }
                    break;
                default :
                    throw new \Exception("gantt类型出错", Error::ERR_GANTT_TYPE);
                    break;
            }
            $model->text       = $params['text'];
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

    public function actionTaskDel($id)
    {
        try {
            $action_type = "deleted";

            list($model, $type) = $this->findModelList($id);
            switch ($type) {
                case Project::LEVEL_PROJECT :
                    $check = Task::find()
                        ->andWhere(["parent" => $model->id])
                        ->one();
                    if (($model->progress == 0) || is_null($check)) {
                        $delete_flag = true;
                    } 
                    break;
                case Project::LEVEL_TASK :
                    $check = Action::find()
                        ->andWhere(["task_id" => $model->id])
                        ->one();
                    if (($model->progress == 0) || is_null($check)) {
                        $delete_flag = true;
                    } 
                    break;
                case Project::LEVEL_ACTION :
                    if ($model->status == STATUS_INIT) {
                        $delete_flag = true;
                    } else {
                        throw new \Exception("只有未进行的行动可以删除", Error::ERR_DEL);
                    }
                    break;
            }
            if ($delete_flag) {
                $result = $model->delete(); 
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                $model->del = Constants::SOFT_DEL_YES; 
                $model->modelValidSave();
            }
            $ret = $this->prepareResponse($action_type, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionLinkAdd()
    {
        try {
            // 新增一条task记录
            $model = new GanttLinks;
            $action_type = "inserted";
            $params_conf = [
                "source" => [null, true],
                "target" => [null, true],
                "type"   => [null, true],
            ];
            $params        = $this->getParamsByConf($params_conf, 'post');
            $model->source = $params['source'];
            $model->target = $params['target'];
            $model->type   = $params['type'];
            $model->modelValidSave();
            $ret = $this->prepareResponse($action_type, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionLinkUpdate($linkid)
    {
        try {
            $model = $this->findModel($linkid, GanttLinks::class);
            $action_type = "updated";
            $params_conf = [
                "source" => [null, true],
                "target" => [null, true],
                "type"   => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->source = $params['source'];
            $model->target = $params['target'];
            $model->type   = $params['type'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $linkid);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $linkid, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionLinkDel($linkid)
    {
        try {
            $action_type = "deleted";
            $model = $this->findModel($linkid, GanttLinks::class);
            $result = $model->delete(); 
            if (!$result) {
                throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            $ret = $this->prepareResponse($action_type, $linkid);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $linkid, $e->getMessage());
            return $this->directJson($ret);
        }
    }
}
