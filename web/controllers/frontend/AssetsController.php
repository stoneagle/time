<?php

namespace app\controllers\frontend;

use Yii;
use app\models\FieldObj;
use app\models\AssetsEntity;
use app\models\AssetsInfo;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class AssetsController extends BaseFieldController
{
    public function actionIndex()
    {
        $model = new FieldObj; 
        $model->user_id = $this->user_obj->id;
        $model->field_id = Area::FIELD_ASSET;
        $result = $model->getQuery()->asArray()->all();
        $list = [];
        $chunk_dict = ArrayHelper::index($result, "id");
        foreach ($result as $one) {
            list($x, $y, $width, $height) = explode(",", $one["position"]);
            $list[] = [
                "id" => $one['id'],
                "x" => $x,
                "y" => $y,
                "width" => $width,
                "height" => $height,
            ];
        }
        $entity_dict = AssetsEntity::getChildDict();

        return $this->render('index', [
            // 优先级
            "priority_dict" => Target::$priority_arr,
            // 实体类别
            "entity_dict" => $entity_dict,
            // 资产区块
            "chunk_list"     => json_encode($list),
            "chunk_dict"     => json_encode($chunk_dict),
            "init_chunk_num" => count($result),
            "last_one_id"    => count($result) == 1 ? current($result)["id"] : 0,
        ]);
    }

    // 新增资产与项目
    public function actionAdd()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $params_conf = [
                "name"        => [null, true],
                "entity_ids"  => [null, true],
                "priority_id" => [null, true],
                "head_count"  => [null, true],
                "value"       => [null, true],
                "time_span"   => [null, true],
                "position"    => [null, true],
                "income_flow" => [null, true],
            ];
            $params              = $this->getParamsByConf($params_conf, 'post');
            $model               = new FieldObj;
            $model->project_name = $params["name"];
            $model->entity_ids   = $params["entity_ids"];
            $model->user_id      = $this->user_obj->id;
            $model->field_id     = Area::FIELD_ASSET;
            $model->priority_id  = $params["priority_id"];
            $this->addFieldEntityProject($model);

            $assets_info_model              = new AssetsInfo;
            $assets_info_model->obj_id      = $model->id;
            $assets_info_model->trade_num   = 0;
            $assets_info_model->head_count  = $params["head_count"];
            $assets_info_model->time_span   = $params["time_span"];
            $assets_info_model->value       = $params["value"];
            $assets_info_model->income_flow = $params["income_flow"];
            $assets_info_model->position    = $params["position"];
            $assets_info_model->modelValidSave();

            list($x, $y, $width, $height) = explode(",", $assets_info_model->position);

            $transaction->commit(); 
            return $this->packageJson([
                'id'     => $model->id,
                'x'      => $x,
                'y'      => $y,
                'width'  => $width,
                'height' => $height,
            ], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }

    public function actionDel($id)
    {
        $transaction   = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id, FieldObj::class);
            $assets_info_model = AssetsInfo::find()
                ->andWhere(["obj_id" => $model->id])
                ->one();
            $result = $assets_info_model->delete();
            if (!$result) {
                throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
            }
            $this->rmFieldEntityProject($model);
            $transaction->commit(); 
            return $this->packageJson(['id' => $id], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }
}
