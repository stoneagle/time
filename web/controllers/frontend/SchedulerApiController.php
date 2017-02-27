<?php

namespace app\controllers\frontend;

use app\models\Events;
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
        return $this->directXml($result);
    }

    public function actionAdd($sid)
    {
        try {
            $model = new Events;
            $action_type = "inserted";
            $params_conf = [
                "text"       => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->user_id    = $this->user_obj->id;
            $model->modelValidSave();
            $ret = $this->prepareResponse($action_type, $sid, $model->id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, null, null);
            return $this->directJson($ret);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id, Events::class);
            $action_type = "updated";
            $params_conf = [
                "text"       => [null, true],
                "start_date" => [null, true],
                "end_date"   => [null, true],
            ];
            $params            = $this->getParamsByConf($params_conf, 'post');
            $model->text       = $params['text'];
            $model->start_date = $params['start_date'];
            $model->end_date   = $params['end_date'];
            $model->modelValidSave();

            $ret = $this->prepareResponse($action_type, $id, $id);
            return $this->directJson($ret);
        } catch (\exception $e) {
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
