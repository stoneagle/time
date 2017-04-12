<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Constants;
use app\models\Area;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AreaController extends BaseController
{
    public function actionIndex()
    {
        $field_arr = Area::$field_arr;
        $field_arr_select2 = [];
        foreach ($field_arr as $id => $name) {
            $field_arr_select2["data"][] = [
                "id" => $id,
                "text" => $name,
            ];
        }
        return $this->render('index', [
            "field_list"         => json_encode($field_arr),
            "field_dict_select2" => json_encode($field_arr_select2),
            "field_en_dict"      => json_encode(Area::$field_en_arr),
        ]);
    }

    public function actionAreaCircle($field_id)
    {
        $model          = new Area;
        $model->field_id = $field_id;
        $model->del     = Constants::SOFT_DEL_NO;
        $origin_t = "origin";
        $area_t = Area::tableName();
        $tree = $model->getAreaTreeArr();
        $ret = [
            "name"     => Area::$field_arr[$field_id],
            "children" => $tree,
        ];
        return $this->directJson(json_encode($ret));
    }

    public function actionAreaTreeRoot($field_id)
    {
        $model           = new Area;
        $model->field_id = $field_id;
        $model->del      = Constants::SOFT_DEL_NO;
        $model->parent   = 0;
        $query           = $model->getQuery();
        $result          = $query
            ->asArray()
            ->all();
        $ret = [];
        foreach ($result as $one) {
            $ret[] = [
                "id"       => $one['id'],
                "text"     => $one["name"],
                "children" => !is_null($one["children_id"]) ? true : false
            ];
        }

        return $this->directJson(json_encode($ret));
    }

    public function actionAreaTreeChildren($field_id)
    {
        $params_conf = [
            "id" => [null, true],
        ];
        $params = $this->getParamsByConf($params_conf, 'get');

        $model = new Area;
        $model->field_id = $field_id;
        $model->del     = Constants::SOFT_DEL_NO;
        $model->parent  = $params['id'];
        $query          = $model->getQuery();
        $result = $query->asArray()->all();
        $ret = [];
        foreach ($result as $one) {
            $ret[] = [
                "id"       => $one['id'],
                "text"     => $one["name"],
                "children" => !is_null($one["children_id"]) ? true : false
            ];
        }

        return $this->directJson(json_encode($ret));
    }

    public function actionAddArea()
    {
        try {
            $model = new Area;
            $params_conf = [
                "name"     => [null, true],
                "field_id" => [null, true],
                "parent"   => [null, true],
            ];
            $params          = $this->getParamsByConf($params_conf, 'post');
            $model->name     = $params['name'];
            $model->field_id = $params['field_id'];
            $model->parent   = $params['parent'];
            //递归计算层级
            $level = 0;
            if ($model->parent != 0) {
                $parent_id = $model->parent;
                do {
                    $parent = Area::findOne($parent_id);
                    if ($parent != null) {
                        $level++;
                        $parent_id = $parent["parent"];
                    } else {
                        throw new \Exception("无法判断层级", Error::ERR_MODEL);
                    }
                } while(!empty($parent_id)); 
            }
            $model->level = $level;
            $model->modelValidSave();

            return $this->packageJson([
                'id'     => $model->id,
            ], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdateArea($id)
    {
        try {
            $params_conf = [
                "name"   => [null, true],
                "parent" => [null, true],
            ];
            $params          = $this->getParamsByConf($params_conf, 'post');
            $model           = $this->findModel($id, Area::class);
            foreach ($params as $index => $one) {
                if (!is_null($one)) {
                    $model->$index = $one;
                }
            }
            $model->modelValidSave();
            return $this->packageJson(['id' => $model->id], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelArea($id)
    {
        try {
            $params_conf = [
                "hard_flag" => [false, false],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');
            $model  = $this->findModel($id, Area::class);
            if ($params['hard_flag']) {
                $result = $model->delete(); 
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                $model->del = Constants::SOFT_DEL_YES; 
                $model->modelValidSave();
            }
            return $this->packageJson(['id' => $params['id']], Error::ERR_OK, Error::msg(Error::ERR_OK));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }
}
