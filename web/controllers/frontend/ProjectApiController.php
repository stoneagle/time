<?php

namespace app\controllers\frontend;

use app\models\Project;
use app\models\Task;
use app\models\Action;
use app\models\GanttLinks;
use app\models\Constants;
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
        $query          = $model->getQuery();
        $data           = $query->asArray()->all();
        foreach ($data as $one) {
            $one['type'] = Project::LEVEL_PROJECT;
            $result[] = $one;
        }

        $task_model          = new Task;
        $task_model->user_id = $this->user_obj->id;
        $query               = $task_model->getQuery();
        $task_data           = $query->asArray()->all();
        foreach ($task_data as $one) {
            $one['type'] = Project::LEVEL_TASK;
            $result[] = $one;
        }

        /* $action_model          = new Action; */
        /* $action_model->user_id = $this->user_obj->id; */
        /* $query                 = $action_model->getQuery(); */
        /* $action_data           = $query->asArray()->all(); */
        /* foreach ($action_data as $one) { */
        /*     $one['type'] = Project::LEVEL_ACTION; */
        /*     $result[] = $one; */
        /* } */

        foreach ($result as &$one) {
            // 控制项目波动
            if ($one['type'] != Project::LEVEL_TASK) {
                $one['duration'] = "";
                $one['sort_date'] = $one['start_date'];
                $one['start_date'] = "";
            } else if ($one['duration'] == 0) {
                $one['unscheduled'] = true;
            }
            // 控制项目展开
            if ($one['type'] != Project::LEVEL_PROJECT) {
                $one['open'] = false;
            }
        }
        $ret['data'] = $result;

        $links           = GanttLinks::find()->asArray()->all();
        $ret['links'] = $links;

        return $this->directJson(json_encode($ret));
    }

    public function actionTaskAdd()
    {
        try {
            $action_type = "inserted";
            $params_conf = [
                "text"             => [null, true],
                "type"             => [null, true],
                "start_date"       => [null, true],
                "end_date"         => [null, true],
                "duration"         => [null, true],
                "progress"         => [0, false],
                "priority_id"      => [0, false],
                "field_id"            => [0, false],
                "parent"           => [null, true],
                "unscheduled_flag" => [null, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            switch ($params['type']) {
                case Project::LEVEL_PROJECT :
                    $model              = new Project;
                    $model->id          = Project::getMaxId();
                    $model->priority_id = $params['priority_id'];
                    $model->field_id    = $params['field_id'];
                    break;
                case Project::LEVEL_TASK :
                    // task与action的id不能跟project重复
                    $model         = new Task;
                    $model->id     = Project::getMaxId();
                    $model->parent = (int)$params['parent'];
                    break;
                default :
                    throw new \Exception("gantt类型出错", Error::ERR_GANTT_TYPE);
                    break;
            }
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            if ($params['unscheduled_flag']) {
                $model->duration   = 0;
            } else {
                $model->duration   = $params['duration'];
            }
            $model->progress    = $params['progress'];
            $model->user_id     = $this->user_obj->id;
            $model->modelValidSave();
            $ret = $this->prepareResponse($action_type, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        }
    }

    public function actionTaskUpdate($id)
    {
        try {
            $model = $this->findModel($id, Project::class);
            $action_type = "updated";
            $params_conf = [
                "text"             => [null, true],
                "start_date"       => [null, true],
                "duration"         => [null, true],
                "progress"         => [0, false],
                "priority_id"      => [0, false],
                "field_id"            => [0, false],
                "parent"           => [null, true],
                "unscheduled_flag" => [null, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            switch ($params['type']) {
                case Project::LEVEL_PROJECT :
                    $model = new Project;
                    $model->priority_id = $params['priority_id'];
                    $model->field_id    = $params['field_id'];
                    break;
                case Project::LEVEL_TASK :
                    $model = new Task;
                    $model->parent      = (int)$params['parent'];
                    break;
                default :
                    throw new \Exception("gantt类型出错", Error::ERR_GANTT_TYPE);
                    break;
            }
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            if ($params['unscheduled_flag']) {
                $model->duration   = 0;
            } else {
                $model->duration   = $params['duration'];
            }
            $model->progress    = $params['progress'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        }
        
    }

    public function actionTaskDel($id)
    {
        try {
            $action_type = "deleted";

            list($model, $type) = $this->findModelList($id);
            $params_conf = [
                "hard_flag" => [false, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            if ($params['hard_flag']) {
                $result = $model->delete(); 
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                $model->del = Constants::SOFT_DEL_YES; 
                $model->modelValidSave();
            }
            $ret = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
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
            $ret         = $this->prepareResponse($action_type);
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

            $ret = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
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
            $ret = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        }
    }
}