<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\ArtEntity;
use app\models\ArtWork;
use app\models\Country;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ArtWorkController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new ArtWork();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new ArtWork();
        $model->load(Yii::$app->request->queryParams);

        $entity_arr = ArtEntity::getChildDict();

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'entityArr'    => $entity_arr,
            'countryArr'   => Country::getDict(),
        ]);
    }

    public function actionCreate()
    {
        $model = new ArtWork();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $entity_arr = ArtEntity::getChildDict();
                return $this->render('save', [
                    'model'      => $model,
                    'entityArr'  => $entity_arr,
                    'countryArr' => Country::getDict(),
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
            $model  = $this->findModel($id, ArtWork::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $entity_arr = ArtEntity::getChildDict();
                return $this->render('save', [
                    'model'      => $model,
                    'id'         => $id,
                    'entityArr'  => $entity_arr,
                    'countryArr' => Country::getDict(),
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
            $query = ArtWork::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                // todo 如果实体已经使用，不允许删除
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
