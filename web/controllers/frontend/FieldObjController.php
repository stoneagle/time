<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\FieldObj;
use app\models\ArtEntity;
use app\models\Constants;
use app\models\FieldObjEntityLink;
use app\models\BaseEntity;
use app\models\Project;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class FieldObjController extends BaseFieldController
{
    public function actionValid()
    {
        try {
            $model = new FieldObj();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionGetEntity()
    {
        $out = [];
        if (isset($_POST['depdrop_all_params']["field_id"])) {
            $field_id = $_POST['depdrop_all_params']["field_id"];
            $model = new FieldObj;
            $out = $model->getEntityDict($field_id);
            return $this->directJson(json_encode(['output'=>$out, 'selected'=>'']));
        }
        return $this->directJson(json_encode(['output'=>'', 'selected'=>'']));
    }

    public function actionIndex()
    {
        $model = new FieldObj();
        $model->user_id = $this->user_obj->id;
        $model->load(Yii::$app->request->queryParams);
        if (empty($model->field_id)) {
            $model->field_id = Config::FIELD_KNOWLEDGE;
        }

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        $field_arr = Config::$field_arr;
        unset($field_arr[Config::FIELD_GENERAL]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'fieldArr'     => $field_arr,
            'priorityArr'  => Config::$priority_arr,
        ]);
    }

    public function actionCreate()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $model = new FieldObj();
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $this->addFieldEntityProject($model);
                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $transaction->commit(); 
                $field_arr = Config::$field_arr;
                unset($field_arr[Config::FIELD_GENERAL]);
                return $this->render('save', [
                    'model'       => $model,
                    'fieldArr'    => $field_arr,
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
            $model  = $this->findModel($id, FieldObj::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                // 删除原有关联
                FieldObjEntityLink::deleteAll(
                    "obj_id = :obj_id",
                    [":obj_id" => $model->id]
                );
                // 新增关联
                foreach ($model->entity_ids as $entity_id) {
                    $obj_entity_link_model            = new FieldObjEntityLink;
                    $obj_entity_link_model->entity_id = $entity_id;
                    $obj_entity_link_model->obj_id    = $model->id;
                    $obj_entity_link_model->modelValidSave();
                }

                $project_model  = Project::find()
                    ->andWhere(["field_id" => $model->field_id])
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
                    ->andWhere(["field_id" => $model->field_id])
                    ->andWhere(["obj_id" => $model->id])
                    ->one();
                $model->project_name = $project_model->text;
                $entity_id_arr = FieldObjEntityLink::find()
                    ->select("entity_id")
                    ->andWhere(["obj_id" => $model->id])
                    ->asArray()->all();
                $model->entity_ids = ArrayHelper::getColumn($entity_id_arr, "entity_id");

                $field_arr = Config::$field_arr;
                unset($field_arr[Config::FIELD_GENERAL]);
                $transaction->commit(); 
                return $this->render('save', [
                    'model'         => $model,
                    'id'            => $id,
                    'initEntityArr' => $model->getEntityDict($model->field_id, BaseEntity::DICT_TYPE_MAP),
                    'entityArr'     => $entity_arr,
                    'fieldArr'      => $field_arr,
                    'priorityArr'   => Config::$priority_arr,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $ids_str = explode(',',$ids);
            $query = FieldObj::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $this->rmFieldEntityProject($model);
            }
            $transaction->commit(); 
            $code = Error::ERR_OK;
            return $this->packageJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnexception($e);
        }
    }
}
