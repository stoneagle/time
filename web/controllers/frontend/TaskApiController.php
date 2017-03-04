<?php

namespace app\controllers\frontend;

use app\models\Project;
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
    public function actionData()
    {
        $model     = new Task;
        $query     = $model->getQuery();
        $task_t    = Task::tableName();
        $project_t = Project::tableName();

        $query->leftJoin($project_t, "$task_t.parent = $project_t.id");

        $query->select("
            $task_t.id, $task_t.text, $project_t.priority_id,
            $project_t.field_id,$project_t.text as project_text
            ")->orderby("$project_t.priority_id, $task_t.ctime");
        $result = $query->asArray()->all();

        $config_model       = new Config;
        $config_model->type = Config::TYPE_FIELD;
        $field_dict         = $config_model->getTypeDict();
        $config_model->type = Config::TYPE_PRIORITY;
        $priority_dict      = $config_model->getTypeDict();

        $ret = [];
        foreach ($result as $one) {
            $one["priority_name"] = ArrayHelper::getValue($priority_dict, $one["priority_id"]); 
            $one["field_name"] = ArrayHelper::getValue($field_dict, $one["field_id"]); 
            $ret["rows"][] = [
                "id" => $one["id"],
                "data" => [
                    $one['field_name'],
                    $one['project_text'],
                    $one['text'],
                    $one['priority_name'],
                    "",
                    "",
                    "0"
                ] 
            ];
        }
        return $this->directJson(json_encode($ret));
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
}
