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
}
