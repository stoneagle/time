<?php

namespace app\controllers\frontend;

use app\models\Error;
use app\models\DailyScheduler;
use app\models\Constants;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class DailySchedulerApiController extends BaseController
{
    // REST接口，获取基础数据
    public function actionData()
    {
        $model          = new DailyScheduler;
        $model->user_id = $this->user_obj->id;
        $model->daily_id = Yii::$app->request->get('daily_id', null);
        if (empty($model->daily_id)) {
          $result = [];
        } else {
          $result = $model->getQuery()->asArray()->all();
        }
        $ret["data"]    = $result;
        return $this->directJson(json_encode($ret));
    }

    public function actionAdd()
    {
        try {
            $action_type = "inserted";
            $model = new DailyScheduler;

            $params_conf = [
                "start_date" => [date("Y-m-d H:i:s", time()), false],
                "end_date"   => [date("Y-m-d H:i:s", time()), false],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->daily_id   = 0;
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
            $model             = $this->findModel($id, DailyScheduler::class);
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
            $model       = $this->findModel($id, DailyScheduler::class);

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
