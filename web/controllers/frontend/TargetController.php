<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Area;
use app\models\Target;
use app\models\Constants;
use app\models\TargetEntityLink;
use app\models\EntityBase;
use app\models\Project;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class TargetController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new Target();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionGetFieldEntity()
    {
        $out = [];
        if (isset($_POST['depdrop_all_params']["field_id"])) {
            $field_id = $_POST['depdrop_all_params']["field_id"];
            $model = new EntityBase;
            $out = $model->getEntityDict($field_id);
            return $this->directJson(json_encode(['output'=>$out, 'selected'=>'']));
        }
        return $this->directJson(json_encode(['output'=>'', 'selected'=>'']));
    }

    public function actionGetEntityDict($id)
    {
        try {
            $model = $this->findModel($id, Target::class);
            $entity_ids_list = TargetEntityLink::find()
                ->select("entity_id")
                ->andWhere(["target_id" => $id])
                ->asArray()->all();
            $entity_ids_arr = ArrayHelper::getColumn($entity_ids_list, "entity_id");
            $entity_model = new EntityBase;
            $dict = $entity_model->getEntityDict($model->field_id, Constants::DICT_TYPE_DHX, $entity_ids_arr, false); 
            $code = Error::ERR_OK;
            return $this->packageJson(['dict' => $dict], $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new Target();
        $model->user_id = $this->user_obj->id;
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'fieldArr'     => Area::$field_arr,
            'priorityArr'  => Target::$priority_arr,
        ]);
    }

    public function actionCreate()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $model = new Target();
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->user_id = $this->user_obj->id;
                $model->addTargetAndLink();
                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $transaction->commit(); 
                return $this->render('save', [
                    'model'       => $model,
                    'fieldArr'    => Area::$field_arr,
                    'priorityArr' => Target::$priority_arr,
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
            $model  = $this->findModel($id, Target::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->updateTargetAndLink();
                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $model->entity_ids = TargetEntityLink::getEntityArrs($model->id);
                $base_entity = new EntityBase;
                $transaction->commit(); 
                return $this->render('save', [
                    'model'         => $model,
                    'id'            => $id,
                    'initEntityArr' => $base_entity->getEntityDict($model->field_id, Constants::DICT_TYPE_MAP),
                    'fieldArr'      => Area::$field_arr,
                    'priorityArr'   => Target::$priority_arr,
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
            $query = Target::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $this->rmTargetAndLink();
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
