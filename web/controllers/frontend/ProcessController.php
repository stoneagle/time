<?php

namespace app\controllers\frontend;

use app\models\Process;
use app\models\GanttTasks;
use app\models\Constants;
use app\models\Error;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ProcessController extends BaseController
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

    public function actionData($id)
    {
        $model = new Process;
        $model->user_id = $this->user_obj->id;
        $model->task_id = $id;
        $data = $model->getTreeNodeList();
        $result = [
            'id'   => $id,
            'item' => $data
        ];
        return $this->directJson(json_encode($result));
    }

    public function actionAdd()
    {
        try {
            $model = new Process;
            $params_conf = [
                "text"       => [null, true],
                "plan_num"   => [null, true],
                "task_id"    => [null, true],
            ];
            $params          = $this->getParamsByConf($params_conf, 'post');
            $model->text     = $params['text'];
            $model->plan_num = $params['plan_num'];
            $model->task_id  = $params['task_id'];
            $model->user_id  = $this->user_obj->id;
            $model->modelValidSave();

            $code = Error::ERR_OK;
            return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            $params_conf = [
                "id"       => [null, true],
                "text"     => [null, true],
                "plan_num" => [null, true],
            ];
            $params          = $this->getParamsByConf($params_conf, 'post');
            $model           = $this->findModel($params['id'], Process::class);
            $model->text     = $params['text'];
            $model->plan_num = $params['plan_num'];
            $model->modelValidSave();

            $code = Error::ERR_OK;
            return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
        
    }

    public function actionDel($id)
    {
        try {
            $model       = $this->findModel($id, Process::class);
            $result      = $model->delete();
            if (!$result) {
                throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            $code = Error::ERR_OK;
            return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionFinishCheck()
    {
        try {
            $params_conf = [
                "ids" => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');

            $list = Process::find()
                ->select("id")
                ->andWhere(["id" => $params['ids']])
                ->andWhere(["finish" => Process::FINISH_TRUE])
                ->asArray()->all();
            $list = ArrayHelper::getColumn($list, 'id');

            $code = Error::ERR_OK;
            return $this->packageJson(['list' => $list], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
        
    }
}
