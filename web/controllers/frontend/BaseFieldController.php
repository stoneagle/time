<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Error;
use app\models\FieldObjEntityLink;
use app\models\Constants;
use app\models\Project;
use app\models\Config;
use app\models\Task;
use app\models\Action;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;

class BaseFieldController extends BaseController 
{
    public function baseEntityIndex($class_name)
    {
        $model = new $class_name();
        $model->load(Yii::$app->request->queryParams);

        $parent_arr = $class_name::getParentDict();

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'parentArr'    => $parent_arr,
        ]);
    }

    public function baseEntityCreate($class_name)
    {
        $model = new $class_name();
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if (empty($model->parent)) {
                $model->parent = 0;
            }
            $model->modelValidSave();
            $code = Error::ERR_OK;
            return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
        } else {
            $parent_arr = $class_name::getParentDict();
            return $this->render('save', [
                'model'   => $model,
                'parentArr'    => $parent_arr,
            ]);
        }
    }

    public function baseEntityUpdate($class_name)
    {
        $id = Yii::$app->request->get('id', null);
        $model  = $this->findModel($id, $class_name);

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
            $parent_arr = $class_name::getParentDict();
            return $this->render('save', [
                'model'        => $model,
                'id'           => $id,
                'parentArr'    => $parent_arr,
            ]);
        }
    }

    public function baseEntityDelete($class_name)
    {
        $ids = Yii::$app->request->post('ids', null);
        if (empty($ids)) {
            throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
        }
        $ids_str = explode(',',$ids);
        $query = $class_name::find()->andWhere(['and',['in', 'id', $ids]]);
        foreach ($query->all() as $model) {
            // todo 如果实体已经使用，不允许删除
            $result = $model->delete();
            if (!$result) {
                throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
        }
        $code = Error::ERR_OK;
        return $this->packageJson($ids, $code, Error::msg($code));
    }

    // 批量天剑field_obj，entity_link，project
    public function addFieldEntityProject(&$field_model)
    {
        $field_model->user_id = $this->user_obj->id;
        $field_model->modelValidSave();
        foreach ($field_model->entity_ids as $entity_id) {
            $obj_entity_link_model = new FieldObjEntityLink; 
            $obj_entity_link_model->entity_id = $entity_id;
            $obj_entity_link_model->obj_id = $field_model->id;
            $obj_entity_link_model->modelValidSave();
        }

        $project_model              = new Project;
        $project_model->id          = Project::getMaxId();
        $project_model->text        = $field_model->project_name;
        $project_model->field_id    = $field_model->field_id;
        $project_model->obj_id      = $field_model->id;
        $project_model->priority_id = $field_model->priority_id;
        $project_model->user_id     = $this->user_obj->id;
        $project_model->progress    = 0;
        $project_model->duration    = 0;
        $project_model->start_date  = Date("Y-m-d 00:00:00", time());
        $project_model->save();
    }

    public function rmFieldEntityProject(&$field_model)
    {
        // 如果实体相关项目已经开始进行，不允许删除
        if (!$field_model->checkDeleteStatus()) {
            throw new \Exception ("该项目已开始执行任务，不能进行删除", Error::ERR_DEL);
        } else {
            // 删除项目
            $project_t = Project::tableName();
            $project_model = Project::find()
                ->andWhere(["$project_t.obj_id" => $field_model->id])
                ->andWhere(["$project_t.field_id" => $field_model->field_id])
                ->one();
            $result = $project_model->delete();
            if (!$result) {
                throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            // 删除关联entity
            FieldObjEntityLink::deleteAll(
                "obj_id = :obj_id",
                [":obj_id" => $field_model->id]
            );
            // 删除自身
            $result = $field_model->delete();
            if (!$result) {
                throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
        }
    }
}
