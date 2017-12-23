<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Daily;
use app\models\DailyScheduler;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class DailyController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new Daily();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new Daily();
        $model->load(Yii::$app->request->queryParams);
        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Daily();
        try {
            if (Yii::$app->request->post()) {
                $daily_arr = Yii::$app->request->post()["Daily"];
                $model->name = $daily_arr["name"];
                $model->user_id = $this->user_obj->id;
                $model->modelValidSave();
                $code = Error::ERR_OK;
                // 更新所有作息表的tmp数据
                $conditions = [
                    "and",
                    ['daily_id' => 0],
                    ['user_id' => $this->user_obj->id],
                ];
                $scheduler_model = new DailyScheduler();
                $scheduler_model->updateAll(["daily_id" => $model->attributes['id']], $conditions);
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                // 删除所有作息表的tmp数据
                $query = DailyScheduler::find()
                    ->andWhere(['daily_id' => 0])
                    ->andWhere(['user_id' => $this->user_obj->id]);
                foreach ($query->all() as $tmp) {
                    $result = $tmp->delete();
                    if (!$result) {
                        throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                    }
                }
                return $this->render('save', [
                    'model'   => $model,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            $model  = $this->findModel($id, Daily::class);

            if (Yii::$app->request->post()) {
                $daily_arr = Yii::$app->request->post()["Daily"];
                $model->name = $daily_arr["name"];
                $model->id = $daily_arr["id"];
                $model->user_id = $this->user_obj->id;
                $model->modelValidSave();
                // 更新所有作息表的tmp数据
                $conditions = [
                    "and",
                    ['daily_id' => 0],
                    ['user_id' => $this->user_obj->id],
                ];
                $scheduler_model = new DailyScheduler();
                $scheduler_model->updateAll(["daily_id" => $model->id], $conditions);

                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                // 删除所有作息表的tmp数据
                $query = DailyScheduler::find()
                    ->andWhere(['daily_id' => 0])
                    ->andWhere(['user_id' => $this->user_obj->id]);
                foreach ($query->all() as $tmp) {
                    $result = $tmp->delete();
                    if (!$result) {
                        throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                    }
                }
                return $this->render('save', [
                    'model'        => $model,
                    'id'           => $id,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        try {
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            // 删除相关作息表
            if (is_array($ids)) {
                foreach ($ids as $tmp_id) {
                    DailyScheduler::deleteAll(
                        "daily_id = :daily_id",
                        [":daily_id" => $tmp_id]
                    );
                } 
            } else {
                DailyScheduler::deleteAll(
                    "daily_id = :daily_id",
                    [":daily_id" => $tmp_id]
                );
            }
            $query = Daily::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $result = $model->delete();
                if (!$result) {
                    throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            }
            $code = Error::ERR_OK;
            return $this->packageJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
