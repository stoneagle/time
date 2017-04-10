<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\BaseEntity;
use app\models\FieldObj;
use app\models\Assets;
use app\models\AssetsSub;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class AssetsSubController extends BaseController
{
    // 获取子资产
    public function actionOneAssetsSub($id)
    {
        $model         = new AssetsSub;
        $model->obj_id = $id;
        $query         = $model->getQuery();
        $result        = $query->asArray()->all();
        return $this->packageJson($result, Error::ERR_OK, Error::msg(Error::ERR_OK));
    }
    
    public function actionValid()
    {
        try {
            $model = new AssetsSub();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionCreate()
    {
        $model = new AssetsSub();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $params_conf = [
                    "obj_id"   => [null, true],
                ];
                $params = $this->getParamsByConf($params_conf, 'get');
                $model->obj_id = $params["obj_id"];
                $field_model = new FieldObj;
                return $this->render('save', [
                    'model'     => $model,
                    'entityArr' => $field_model->getDictByObjId(Config::FIELD_ASSET, $params["obj_id"]),
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
            $model  = $this->findModel($id, AssetsSub::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $field_model = new FieldObj;
                return $this->render('save', [
                    'model'     => $model,
                    'entityArr' => $field_model->getDictByObjId(Config::FIELD_ASSET, $model->obj_id),
                    'id'        => $id,
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
            $query = AssetsSub::find()->andWhere(['and',['in', 'id', $ids]]);
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
