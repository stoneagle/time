<?php

namespace app\controllers\frontend;

use Yii;
use app\models\EntityCircle;
use app\models\Area;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class EntityCircleController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new EntityCircle();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new EntityCircle();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        $area = new Area;
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'areaArr'      => $area->getAreaLeafDict(Area::FIELD_SOCIAL),
        ]);
    }

    public function actionCreate()
    {
        $model = new EntityCircle();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $area_model = new Area;
                return $this->render('save', [
                    'model'     => $model,
                    'areaArr'   => $area_model->getAreaLeafDict(Area::FIELD_SOCIAL),
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
            $model  = $this->findModel($id, EntityCircle::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $area_model = new Area;
                return $this->render('save', [
                    'id'        => $model->id,
                    'model'     => $model,
                    'areaArr'   => $area_model->getAreaLeafDict(Area::FIELD_SOCIAL),
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
            $query = EntityCircle::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $model->checkAndDelEntity();
            }
            $code = Error::ERR_OK;
            return $this->packageJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
