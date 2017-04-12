<?php

namespace app\controllers\frontend;

use Yii;
use app\models\EntityWork;
use app\models\Area;
use app\models\Country;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class EntityWorkController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new EntityWork();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new EntityWork();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        $area = new Area;
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'countryArr'   => Country::getDict(),
            'areaArr'      => $area->getAreaLeafDict(Area::FIELD_CULTURE),
        ]);
    }

    public function actionCreate()
    {
        $model = new EntityWork();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $area = new Area;
                return $this->render('save', [
                    'model'      => $model,
                    'entityArr'  => $entity_arr,
                    'countryArr' => Country::getDict(),
                    'areaArr'    => $area->getAreaLeafDict(Area::FIELD_CULTURE),
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
            $model  = $this->findModel($id, EntityWork::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $area = new Area;
                return $this->render('save', [
                    'model'      => $model,
                    'id'         => $id,
                    'countryArr' => Country::getDict(),
                    'areaArr'    => $area->getAreaLeafDict(Area::FIELD_CULTURE),
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
            $query = EntityWork::find()->andWhere(['and',['in', 'id', $ids]]);
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
