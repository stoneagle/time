<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Assets;
use app\models\AssetsEntity;
use app\models\AssetsLinks;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class AssetsController extends BaseController
{
    public function actionIndex()
    {
        $model = new Assets; 
        $model->user_id = $this->user_obj->id;
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
        $type_raw_dict = AssetsEntity::$type_arr;
        $type_dict = [];
        foreach ($type_raw_dict as $id => $name) {
            $type_dict["data"]["ceshi"] = [
                "id" => $id,
                "text" => $name
            ];
        }
        $priority_raw_dict = Config::$priority_arr;
        $priority_dict = [];
        foreach ($priority_raw_dict as $id => $name) {
            $priority_dict["data"][] = [
                "id" => $id,
                "text" => $name
            ];
        }
        $entity_list = AssetsEntity::find()->asArray()->all();
        $entity_dict = [];
        foreach ($entity_list as $one) {
            $entity_dict[$type_raw_dict[$one["type_id"]]][$one["id"]] = $one["name"];
        }

        $model = new AssetsLinks();
        $model->user_id = $this->user_obj->id;
        $model->load(Yii::$app->request->queryParams);
        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);

        return $this->render('index', [
            // 优先级
            "priority_dict"    => json_encode($priority_dict),
            // 实体类别
            "entity_dict" => $entity_dict,
            // 资产类别
            "type_raw_dict"    => json_encode($type_raw_dict),
            // 评估标准
            "type_access_dict" => json_encode(Assets::$type_access_arr),
            // 资产区块
            "chunk_list"       => json_encode($list),
            "chunk_dict"       => json_encode($chunk_dict),
            "init_chunk_num"   => count($result),
            "last_one_id"      => count($result) == 1 ? current($result)["id"] : 0,
            // 项目列表
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
        ]);
    }
}
