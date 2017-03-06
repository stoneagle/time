<?php

namespace app\controllers\frontend;

use app\models\Events;
use app\models\GanttTasks;
use app\models\Process;
use app\models\Constants;
use Yii;
use yii\filters\VerbFilter;

class SchedulerApiController extends BaseController
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
        $model  = new Events;
        $query  = $model->getQuery();
        $result = $query->asArray()->all();
        foreach ($result as &$one) {
            $one['info'] = "[".$one['task_name']."]".$one['process_name'];
        }
        return $this->directXml($result);
    }

    public function actionAdd($sid)
    {
        try {
            $model = new Events;
            $transaction   = Yii::$app->db->beginTransaction();

            $action_type = "inserted";
            $params_conf = [
                "text"       => [null, true],
                "process_id" => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
                "finish"     => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->process_id = $params['process_id'];
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->user_id    = $this->user_obj->id;
            $model->modelValidSave();

            $process_model = $this->findModel($model->process_id, Process::class);
            if ($params['finish'] == Process::FINISH_TRUE) {
                $process_model->finish = Process::FINISH_TRUE;
            } else {
                $process_model->finish = Process::FINISH_NO;
            }
            $process_model->modelValidSave();
            $task_model = $this->findModel($process_model->task_id, GanttTasks::class);
            $task_model->process_id = $process_model->id;
            $task_model->checkAndChangeDuration();

            $transaction->commit(); 
            $ret = $this->prepareResponse($action_type, $sid, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $transaction->rollBack(); 
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, null);
            return $this->directJson($ret);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id, Events::class);
            $transaction   = Yii::$app->db->beginTransaction();

            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "process_id" => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
                "finish"     => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->process_id = $params['process_id'];
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->modelValidSave();

            $process_model = $this->findModel($model->process_id, Process::class);
            if ($params['finish'] == Process::FINISH_TRUE) {
                $process_model->finish = Process::FINISH_TRUE;
            } else {
                $process_model->finish = Process::FINISH_NO;
            }
            $process_model->modelValidSave();
            $task_model = $this->findModel($process_model->task_id, GanttTasks::class);
            $task_model->process_id = $process_model->id;
            $task_model->checkAndChangeDuration();

            $transaction->commit(); 
            $ret = $this->prepareResponse($action_type, $id, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $transaction->rollBack(); 
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $id);
            return $this->directJson($ret);
        }
        
    }

    public function actionDel($id)
    {
        try {
            $action_type = "deleted";
            $model       = $this->findModel($id, Events::class);
            $result      = $model->delete();
            if (!$result) {
                throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            $ret = $this->prepareResponse($action_type, $id, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $id);
            return $this->directJson($ret);
        }
    }

    private function prepareResponse($action, $sid, $tid)
    {
        $result = array(
            'action' => $action,
            'sid' => $sid,
            'tid' => $tid
        );
        return json_encode($result);
    }
}
