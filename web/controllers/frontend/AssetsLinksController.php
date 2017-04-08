<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\AssetsLinks;
use app\models\AssetsSub;
use app\models\Project;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class AssetsLinksController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new AssetsLinks();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionCreate()
    {
        $model = new AssetsLinks();
        try {
            $transaction   = Yii::$app->db->beginTransaction();
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->entity_ids = implode(',', $model->entity_ids);
                $model->user_id = $this->user_obj->id;
                $model->modelValidSave();

                $project_model              = new Project;
                $project_model->id          = Project::getMaxId();
                $project_model->text        = $model->project_name;
                $project_model->field_id    = Config::FIELD_ASSET;
                $project_model->obj_id      = $model->id;
                $project_model->priority_id = $model->priority_id;
                $project_model->user_id     = $this->user_obj->id;
                $project_model->progress    = 0;
                $project_model->duration    = 1;
                $project_model->start_date  = Date("Y-m-d 00:00:00", time());
                $project_model->save();

                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $params_conf = [
                    "assets_id"   => [null, true],
                ];
                $params = $this->getParamsByConf($params_conf, 'get');
                $model->assets_id = $params["assets_id"];
                $entity_arr = AssetsSub::getDict($params["assets_id"]);
                $transaction->commit(); 
                return $this->render('save', [
                    'model'     => $model,
                    'entityArr' => $entity_arr,
                    'priorityArr' => Config::$priority_arr,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $id = Yii::$app->request->get('id', null);
            $model  = $this->findModel($id, AssetsLinks::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->entity_ids = implode(',', $model->entity_ids);
                $model->modelValidSave();

                $project_model  = Project::find()
                    ->andWhere(["field_id" => Config::FIELD_ASSET])
                    ->andWhere(["obj_id" => $model->id])
                    ->one();
                $project_model->text        = $model->project_name;
                $project_model->priority_id = $model->priority_id;
                $project_model->modelValidSave();

                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $project_model  = Project::find()
                    ->andWhere(["field_id" => Config::FIELD_ASSET])
                    ->andWhere(["obj_id" => $model->id])
                    ->one();
                $model->project_name = $project_model->text;
                $model->entity_ids = explode(',', $model->entity_ids);

                $params_conf = [
                    "assets_id"   => [null, true],
                ];
                $params = $this->getParamsByConf($params_conf, 'get');
                $entity_arr = AssetsSub::getDict($params["assets_id"]);
                $transaction->commit(); 
                return $this->render('save', [
                    'model'        => $model,
                    'id'           => $id,
                    'entityArr'    => $entity_arr,
                    'priorityArr' => Config::$priority_arr,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack(); 
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
            $query = AssetsLinks::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                // todo 如果实体相关项目已经开始进行，不允许删除
                throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
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
