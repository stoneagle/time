<?php

namespace app\controllers\frontend;

use app\models\Error;
use app\models\Task;
use app\models\PlanScheduler;
use app\models\PlanTask;
use app\models\Action;
use app\models\Constants;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class PlanApiController extends BaseController
{
    // REST接口，获取基础数据
    public function actionData()
    {
        $model          = new PlanScheduler;
        $model->user_id = $this->user_obj->id;
        $result = $model->getQuery()->asArray()->all();
        $ret["data"] = $result;
        return $this->directJson(json_encode($ret));
    }

    public function actionChart($year, $week)
    {
        $model = new PlanScheduler;
        $model->user_id = $this->user_obj->id;

        $task_model          = new Task;
        $task_model->user_id = $this->user_obj->id;
        $task_model->del     = Constants::SOFT_DEL_NO;
        $query               = $task_model->getPlanTask();

        $now = date("Y-W", time());
        $date = $year. "-" .$week;
        if (empty($week) || ($date == $now)) {
            $model->week = $now;
            $query->andWhere(["week" => $now]);
        } else {
            $model->week = $date;
            $query->andWhere(["week" => $week]);
        }
        $result = $model->getQuery()->asArray()->all();
        $task_result = $query->asArray()->all();

        // 处理scheduler的预估时间
        $tmp_arr = [];
        $ret = [];
        $tmp_arr["time"] = [];
        foreach ($result as $one) {
            $minutes = \DateUtil::minuteBetween($one['start_date'], $one['end_date']);
            $week_num = \DateUtil::getWeekNum($one["start_date"]);
            $tmp_arr["time"][$week_num] += $minutes;
        }
        $ret["week_time"] = [];
        $ret["week_time_sum"] = 0;
        foreach ($tmp_arr["time"] as $week_num => $time) {
            $ret["week_time"][] = [
                "value" => $time,
                "name" => $week_num,
            ];
            $ret["week_time_sum"] += $time;
        }
        
        // 处理该周已选任务的数量，以及消耗时间
        $config_model       = new Config;
        $config_model->type = Config::TYPE_FIELD;
        $field_dict         = $config_model->getTypeDict();

        $tmp_arr["field"] = [];
        foreach ($task_result as $one) {
            $field_name = ArrayHelper::getValue($field_dict, $one["field_id"]);
            $tmp_arr["field"][$field_name] += $one["sum_time"] * 30;
        }

        $ret["field_time_x"] = isset($tmp_arr["field"]) ? array_keys($tmp_arr["field"]) : [];
        $ret["field_time"] = [];
        $ret["field_time_sum"] = 0;
        foreach ($tmp_arr["field"] as $field_name => $time) {
            $ret["field_time"][] = [
                "value" => $time,
                "name" => $field_name,
            ];
            $ret["field_time_sum"] += $time;
        }
        return $this->directJson(json_encode($ret));
    }

    public function actionAdd()
    {
        try {
            $action_type = "inserted";
            $model = new PlanScheduler;

            $params_conf = [
                "start_date" => [date("Y-m-d H:i:s", time()), false],
                "end_date"   => [date("Y-m-d H:i:s", time()), false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->week       = date("Y-W", strtotime($params["start_date"]));
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
                "start_date" => [date("Y-m-d H:i:s", time()), false],
                "end_date"   => [date("Y-m-d H:i:s", time()), false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model             = $this->findModel($id, PlanScheduler::class);
            $model->week       = date("Y-W", strtotime($params["start_date"]));
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $id, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    public function actionDel($id)
    {
        try {
            $action_type = "deleted";
            $model       = $this->findModel($id, PlanScheduler::class);

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

    public function actionTaskOper($task_id)
    {
        try {
            $params_conf = [
                "week"   => [null, true],
                "select" => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model = new PlanTask;
            if ($params["select"] == 0) {
                // 撤除某项计划
                $action_type = "deleted";
                $model = $model->find()
                    ->andWhere(["task_id" => $task_id])
                    ->andWhere(["week" => $params["week"]])
                    ->one();
                if (is_null($model)) {
                    throw new \Exception(Error::msg(Error::ERR_MODEL), Error::ERR_MODEL);
                }
                $result      = $model->delete();
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                // 新增某项计划
                $action_type = "inserted";
                $model->week    = $params["week"];
                $model->task_id = $task_id;
                $model->user_id = $this->user_obj->id;
                $model->modelValidSave();
            }

            $ret = $this->prepareResponse($action_type, $task_id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $task_id, $e->getMessage());
            return $this->directJson($ret);
        }
        
    }
}
