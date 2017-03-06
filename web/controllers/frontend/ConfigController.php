<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ConfigController extends BaseController
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

    public function actionIndex()
    {
        $model = new Config();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'typeArr'      => Config::$type_arr,
            'parentArr'    => Config::getParentList(),
        ]);
    }

    public function actionValid()
    {
        try {
            $model = new Config();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionCreate()
    {
        $model = new Config();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                if (empty($model->parent)) {
                    $model->parent = 0;
                }
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                return $this->render('save', [
                    'model'   => $model,
                    'typeArr' => Config::$type_arr,
                    'parentArr' => Config::getParentList(),
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $model  = $this->findModel($id, Config::class);
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                return $this->render('save', [
                    'model'   => $model,
                    'typeArr' => Config::$type_arr,
                    'id'      => $id,
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
            $ids_str = explode(',',$ids);
            $query = Config::find()->andWhere(['and',['in', 'id', $ids]]);
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
