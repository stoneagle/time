<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\BusinessAssets;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AssetsController extends BaseController
{
    public function actionIndex()
    {
        $model = new BusinessAssets; 
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
        $type_raw_dict = BusinessAssets::$type_arr;
        $type_dict = [];
        foreach ($type_raw_dict as $id => $name) {
            $type_dict["data"][] = [
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
        return $this->render('index', [
            "priority_dict"    => json_encode($priority_dict),
            "type_dict"        => json_encode($type_dict),
            "type_raw_dict"    => json_encode($type_raw_dict),
            "type_access_dict" => json_encode(BusinessAssets::$type_access_arr),
            "chunk_list"       => json_encode($list),
            "chunk_dict"       => json_encode($chunk_dict),
            "list_type_dict"   => json_encode($type_dict),
            "init_chunk_num"   => count($result),
            "last_one_id"      => count($result) == 1 ? current($result)["id"] : 0,
        ]);
    }
}
