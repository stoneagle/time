<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\KnowledgeArea;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class KnowledgeAreaController extends BaseController
{
    public function actionIndex()
    {
        $area_arr = Config::$area_arr;
        $area_dict_select2 = [];
        foreach ($area_arr as $id => $name) {
            $area_dict_select2["data"][] = [
                "id" => $id,
                "text" => $name,
            ];
        }
        return $this->render('index', [
            "area_list"         => json_encode($area_arr),
            "area_dict_select2" => json_encode($area_dict_select2),
            "area_en_dict"      => json_encode(Config::$area_en_arr),
        ]);
    }
}
