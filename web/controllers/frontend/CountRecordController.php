<?php

namespace app\controllers\frontend;

use app\models\Constants;
use app\models\Action;
use app\models\CountRecord;
use app\models\Error;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class CountRecordController extends BaseController
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

    public function actionOne()
    {
        try {
            $params_conf = [
                "id" => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');
            $model = new CountRecord;
            $model->user_id = $this->user_obj->id;
            $model->id = $params['id'];
            $result = $model->getOne();
            return $this->packageJson(['info' => $result], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionAdd()
    {
        try {
            $model = new CountRecord;

            $params_conf = [
                "action_id" => [null, true],
                "status"  => [CountRecord::STATUS_EXEC, false],
            ];
            $params           = $this->getParamsByConf($params_conf, 'post');
            $model->action_id = $params['action_id'];
            $model->status    = $params['status'];
            $model->init_time = 0;
            $model->user_id   = $this->user_obj->id;
            $model->modelValidSave();

            return $this->packageJson(['id' => $model->attributes['id']], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            // 每个用户只会保留一条最新的执行中记录
            $transaction   = Yii::$app->db->beginTransaction();
            $model = CountRecord::find()
                ->andWhere([
                    'status' => [
                        CountRecord::STATUS_EXEC,
                        CountRecord::STATUS_PAUSE,
                    ]
                ])
                ->andWhere(['user_id' => $this->user_obj->id])
                ->orderBy("id desc")
                ->one();

            $params_conf = [
                "init_time" => [null, true],
                "status"    => [null, false],
            ];
            $params           = $this->getParamsByConf($params_conf, 'post');
            $model->status    = $params['status'];
            $model->init_time = $params['init_time'];
            $model->modelValidSave();

            $action_model = $this->findModel($model->action_id, Action::class);
            switch ($model->status) {
                case CountRecord::STATUS_FINISH :
                    $action_model->status = Action::STATUS_END;
                    $action_model->modelValidSave();
                    break;
                case CountRecord::STATUS_CANCEL :
                    $action_model->status = Action::STATUS_WAIT;
                    $action_model->modelValidSave();
                    break;
                default :
                    break;
            }

            $transaction->commit(); 
            return $this->packageJson(['id' => $model->attributes['id']], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }
}
