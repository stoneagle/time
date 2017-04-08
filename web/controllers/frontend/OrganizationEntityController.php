<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\OrganizationEntity;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class OrganizationEntityController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new OrganizationEntity();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new OrganizationEntity();
        $model->load(Yii::$app->request->queryParams);

        $parent_arr = OrganizationEntity::getParentDict();

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'parentArr'    => $parent_arr,
        ]);
    }

    public function actionCreate()
    {
        $model = new OrganizationEntity();
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
                $parent_arr = OrganizationEntity::getParentDict();
                return $this->render('save', [
                    'model'   => $model,
                    'parentArr'    => $parent_arr,
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
            $model  = $this->findModel($id, OrganizationEntity::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                if (empty($model->parent)) {
                    $model->parent = 0;
                }
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                if (empty($model->parent)) {
                    $model->parent = "";
                }
                $parent_arr = OrganizationEntity::getParentDict();
                return $this->render('save', [
                    'model'        => $model,
                    'id'           => $id,
                    'parentArr'    => $parent_arr,
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
            $query = OrganizationEntity::find()->andWhere(['and',['in', 'id', $ids]]);
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
