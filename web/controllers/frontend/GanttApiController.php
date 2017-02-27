<?php

namespace app\controllers\frontend;

use app\models\GanttTasks;
use app\models\GanttLinks;
use app\models\Constants;
use Yii;
use yii\filters\VerbFilter;

class GanttApiController extends BaseController
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
        $model          = new GanttTasks;
        $model->user_id = $this->user_obj->id;
        $query          = $model->getQuery();
        $data           = $query->asArray()->all();
        foreach ($data as &$one) {
            // 控制项目波动
            if ($one['type'] != GanttTasks::LEVEL_TASK) {
                $one['duration'] = "";
            }
        }
        $result['data'] = $data;

        $links           = GanttLinks::find()->asArray()->all();
        $result['links'] = $links;

        return $this->directJson(json_encode($result));
    }

    public function actionTaskAdd()
    {
        try {
            $model = new GanttTasks;
            $action_type = "inserted";
            $params_conf = [
                "text"       => [null, true],
                "type"       => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
                "duration"   => [null, true],
                "progress"   => [0, false],
                "action"     => [0, false],
                "field"      => [0, false],
                "parent"     => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            $model->duration   = $params['duration'];
            $model->progress   = $params['progress'];
            $model->parent     = (int)$params['parent'];
            $model->type       = $params['type'];
            $model->action_id  = $params['action'];
            $model->field_id   = $params['field'];
            $model->user_id    = $this->user_obj->id;
            $model->modelValidSave();
            $ret = $this->prepareResponse($action_type, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        }
    }

    public function actionTaskUpdate($taskid)
    {
        try {
            $model = $this->findModel($taskid, GanttTasks::class);
            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "start_date" => [null, true],
                "duration"   => [null, true],
                "progress"   => [0, false],
                "action"     => [0, false],
                "field"      => [0, false],
                "parent"     => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            $model->duration   = $params['duration'];
            $model->progress   = $params['progress'];
            $model->action_id  = $params['action'];
            $model->field_id   = $params['field'];
            $model->parent     = (int)$params['parent'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type);
            return $this->directJson($ret);
        }
        
    }

    public function actionTaskDel($taskid)
    {
        try {
            $action_type = "deleted";
            $model = $this->findModel($taskid, GanttTasks::class);
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
            $model = $this->findModel($taskid, GanttLinks::class);
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
            $model = $this->findModel($taskid, GanttLinks::class);
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

    private function prepareResponse($action, $tid = null)
    {
        $result = array(
            'action' => $action
        );
        if(isset($tid) && !is_null($tid)){
            $result['tid'] = $tid;
        }
        return json_encode($result);
    }

    public function actionTaskTree()
    {
        $model          = new GanttTasks;
        $model->user_id = $this->user_obj->id;
        $data           = $model->getTree();
        $result = [
            'id'   => 0,
            'item' => $data
        ];
        return $this->directJson(json_encode($result));
    }
}
