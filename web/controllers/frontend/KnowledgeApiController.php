<?php

namespace app\controllers\frontend;

use app\models\Error;
use app\models\Constants;
use app\models\Config;
use app\models\KnowledgeArea;
use app\models\UserSkillLink;
use app\models\Task;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class KnowledgeApiController extends BaseController
{
    public function actionAreaCircle($area_id)
    {
        $model          = new KnowledgeArea;
        $model->area_id = $area_id;
        $model->del     = Constants::SOFT_DEL_NO;
        $origin_t = "origin";
        $area_t = KnowledgeArea::tableName();
        $tree = $model->getAreaTreeArr();
        $ret = [
            "name" => Config::$area_arr[$area_id],
            "children" => $tree,
        ];
        return $this->directJson(json_encode($ret));
    }

    public function actionAreaTreeRoot($area_id)
    {
        $model          = new KnowledgeArea;
        $model->area_id = $area_id;
        $model->del     = Constants::SOFT_DEL_NO;
        $model->parent  = 0;
        $query          = $model->getQuery();
        $result         = $query
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

    public function actionAreaTreeChildren($area_id)
    {
        $params_conf = [
            "id" => [null, true],
        ];
        $params = $this->getParamsByConf($params_conf, 'get');

        $model = new KnowledgeArea;
        $model->area_id = $area_id;
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
            $model = new KnowledgeArea;
            $params_conf = [
                "name"    => [null, true],
                "area_id" => [null, true],
                "parent"  => [null, true],
            ];
            $params         = $this->getParamsByConf($params_conf, 'post');
            $model->name    = $params['name'];
            $model->area_id = $params['area_id'];
            $model->parent  = $params['parent'];
            //递归计算层级
            $level = 0;
            if ($model->parent != 0) {
                $parent_id = $model->parent;
                do {
                    $parent = KnowledgeArea::findOne($parent_id);
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
            $model           = $this->findModel($id, KnowledgeArea::class);
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
            $model  = $this->findModel($id, KnowledgeArea::class);
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

    public function actionAddUserSkill()
    {
        try {
            $params_conf = [
                "skill_id" => [null, true],
                "level"    => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');
            $model           = new UserSkillLink;
            $model->user_id  = $this->user_obj->id;
            $model->skill_id = $params["skill_id"];
            $model->level    = $params["level"];
            $obj = $model->getQuery()->one();
            if (is_null($obj)) {
                $model->modelValidSave();
            }
            $code = Error::ERR_OK;
            return $this->packageJson(['id' => $model->id], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionRemoveUserSkill()
    {
        try {
            $params_conf = [
                "skill_id" => [null, true],
                "level"    => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');

            $model           = new UserSkillLink;
            $model->user_id  = $this->user_obj->id;
            $model->skill_id = $params["skill_id"];
            $model->level    = $params["level"];
            $obj = $model->getQuery()->one();
            if (!is_null($obj)) {
                $task_model = new Task;
                if (is_null($task_model->getTaskByEntityId(Config::FIELD_KNOWLEDGE, $obj->id))) {
                     throw new \Exception ("该技能已开始训练，不允许删除", Error::ERR_DEL);               
                } else {
                    $result = $obj->delete();
                    if (!$result) {
                        throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                    }
                }
            }
            $code = Error::ERR_OK;
            return $this->packageJson([], $code, Error::msg($code));
        } catch (\exception $e) {
            return $this->returnException($e);
        }
    }
}
