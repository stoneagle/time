<?php

namespace app\controllers\frontend;

use app\models\Error;
use app\models\BusinessAssets;
use app\models\Project;
use app\models\Action;
use app\models\Constants;
use app\models\Config;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AssetsApiController extends BaseController
{
    public function actionData($id)
    {
        $model = new Project;
        $model->user_id = $this->user_obj->id;
        $model->obj_id = $id;
        $model->field_id = Config::FIELD_ASSET;
        $action_t  = Action::tableName();
        $project_t = Project::tableName();
        $query = $model->getQuery()
            ->select("$project_t.*, sum($action_t.exec_time) as sum_time")
            ->groupBy("$project_t.id");
        $result = $query->asArray()->all();

        return $this->packageJson($result, Error::ERR_OK, Error::msg(Error::ERR_OK));
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
        $field_dict         = Config::$field_arr;

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
            $model = new BusinessAssets;
            $params_conf = [
                "name"        => [null, true],
                "type_id"     => [null, true],
                "value"       => [null, true],
                "time"        => [null, true],
                "position"    => [null, true],
                "access_unit" => [null, true],
            ];
            $params             = $this->getParamsByConf($params_conf, 'post');
            $model->name        = $params['name'];
            $model->type_id     = $params['type_id'];
            $model->value       = $params['value'];
            $model->time        = $params['time'];
            $model->position    = $params['position'];
            $model->access_unit = $params['access_unit'];
            $model->user_id     = $this->user_obj->id;
            $model->modelValidSave();

            list($x, $y, $width, $height) = explode(",", $model->position);

            return $this->packageJson([
                'id'     => $model->id,
                'x'      => $x,
                'y'      => $y,
                'width'  => $width,
                'height' => $height,
            ], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $params_conf = [
                "name"        => [null, false],
                "type_id"     => [null, false],
                "value"       => [null, false],
                "time"        => [null, false],
                "position"    => [null, true],
                "access_unit" => [null, false],
            ];
            $params          = $this->getParamsByConf($params_conf, 'post');
            $model           = $this->findModel($id, BusinessAssets::class);
            foreach ($params as $index => $one) {
                if (!is_null($one)) {
                    $model->$index = $one;
                }
            }
            $model->modelValidSave();
            return $this->packageJson(['id' => $model->id], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDel($id)
    {
        try {
            $params_conf = [
                "hard_flag" => [false, false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model       = $this->findModel($id, BusinessAssets::class);
            if ($params['hard_flag']) {
                $result = $model->delete(); 
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                $model->del = Constants::SOFT_DEL_YES; 
                $model->modelValidSave();
            }
            return $this->packageJson(['id' => $id], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }
}
