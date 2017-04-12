<?php

namespace app\controllers\frontend;

use Yii;
use app\models\EntitySkill;
use app\models\DependSkillLink;
use app\models\AreaSkillLink;
use app\models\Area;
use app\models\Error;
use app\models\UserSkillLink;
use app\models\Constants;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class EntitySkillController extends BaseController
{
    public function actionIndex()
    {
        $area_id     = Yii::$app->request->get('area_id', null);
        $model       = new EntitySkill;
        $skill_t     = EntitySkill::tableName();
        $link_t      = AreaSkillLink::tableName();
        $depend_t    = DependSkillLink::tableName();
        $user_link_t = UserSkillLink::tableName();
        $result      = $model->getQuery()
            ->select("$skill_t.*, GROUP_CONCAT($depend_t.depend_id) as depend_ids,  max($user_link_t.level) as user_level")
            ->leftJoin($link_t, "$skill_t.id = $link_t.skill_id")
            ->leftJoin($depend_t, "$skill_t.id = $depend_t.skill_id")
            ->leftJoin($user_link_t, "$user_link_t.skill_id = $skill_t.id")
            ->andWhere(["$link_t.area_id" => $area_id])
            ->groupBy("$skill_t.id")
            ->asArray()->all();
        $skill_list = [];
        $skill_position = [];
        foreach ($result as $one) {
            if (!isset($skill_position[$one["type_id"]])) {
                $skill_position[$one["type_id"]] = 0;
            } else {
                $skill_position[$one["type_id"]]++;
            }
            $type_skill_nums = $skill_position[$one["type_id"]];
            $left            = $type_skill_nums * Skill::SKILL_WIDTH;
            $top             = Skill::SKILL_INIT_HEIGHT 
                + floor($type_skill_nums/Skill::SKILL_WIDTH_NUM) * Skill::SKILL_HEIGHT
                + ($one["type_id"] - 1) * Skill::SKILL_TYPE_HEIGHT;
            
            $skill_list[] = [
                "id"               => (int)$one["id"],
                "title"            => $one["name"],
                "description"      => $one["description"],
                "rankDescriptions" => array_values(Skill::$default_level_desc),
                "links"            => [],
                "dependsOn"        => is_null($one["depend_ids"]) ? [] : explode(",", $one["depend_ids"]),
                "maxPoints"        => Skill::DEFAULT_LEVEL_MAX,
                "points"           => $one["user_level"],
                "stats"            => [],
                "margin_left"      => $left."px",
                "margin_top"       => $top."px",
            ];
        }

        return $this->render('index', [
            "skill_list" => json_encode($skill_list),
            "type_dict_arr"  => Skill::$type_arr,
        ]);
    }

    public function actionValid()
    {
        try {
            $model = new Skill();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionConfigIndex()
    {
        $model = new EntitySkill();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('config-index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'typeArr'      => EntitySkill::$type_arr,
        ]);
    }

    public function actionConfigCreate()
    {
        $model = new EntitySkill();
        try {
            $transaction   = Yii::$app->db->beginTransaction();
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                
                foreach ($model->area_ids as $area_id) {
                    $area_link_model = new AreaSkillLink;
                    $area_link_model->area_id = $area_id;
                    $area_link_model->skill_id = $model->id;
                    $area_link_model->modelValidSave();
                }

                if (!empty($model->depend_ids)) {
                    foreach ($model->depend_ids as $depend_id) {
                        $depend_model = new DependSkillLink; 
                        $depend_model->skill_id = $model->id;
                        $depend_model->depend_id = $depend_id;
                        $depend_model->modelValidSave();
                    }
                }

                $transaction->commit(); 
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $skill_list = EntitySkill::find()->select("id, name")->asArray()->all();
                $skill_list = ArrayHelper::map($skill_list,"id","name");

                $area      = new Area;
                $area->del = Constants::SOFT_DEL_NO;
                $area_dict = $area->getAreaLeafDict(Area::FIELD_KNOWLEDGE);

                $model->max_points = 4;
                $transaction->commit(); 
                return $this->render('config-save', [
                    'model'        => $model,
                    'typeArr'      => EntitySkill::$type_arr,
                    'dependSkills' => $skill_list,
                    'areaDict'     => $area_dict,
                    'talents'      => [],
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }

    public function actionConfigUpdate()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            $model  = $this->findModel($id, EntitySkill::class);
            $transaction   = Yii::$app->db->beginTransaction();

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();

                // 删除原有关联area_link
                AreaSkillLink::deleteAll(
                    "skill_id = :skill_id",
                    [":skill_id" => $model->id]
                );
                // 删除原有关联depend_link
                DependSkillLink::deleteAll(
                    "skill_id = :skill_id",
                    [":skill_id" => $model->id]
                );

                foreach ($model->area_ids as $area_id) {
                    $area_link_model = new AreaSkillLink;
                    $area_link_model->area_id = $area_id;
                    $area_link_model->skill_id = $model->id;
                    $area_link_model->modelValidSave();
                }

                if (!empty($model->depend_ids)) {
                    foreach ($model->depend_ids as $depend_id) {
                        $depend_model = new DependSkillLink; 
                        $depend_model->skill_id = $model->id;
                        $depend_model->depend_id = $depend_id;
                        $depend_model->modelValidSave();
                    }
                }

                $code = Error::ERR_OK;
                $transaction->commit(); 
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                $skill_list = EntitySkill::find()->select("id, name")->asArray()->all();
                $skill_list = ArrayHelper::map($skill_list,"id","name");
                if (isset($skill_list[$model->id])) {
                    unset($skill_list[$model->id]);
                }

                $area = new Area;
                $area_dict = $area->getAreaLeafDict(Area::FIELD_KNOWLEDGE);

                $depend_list = DependSkillLink::find()
                    ->andWhere(["skill_id" => $model->id])
                    ->asArray()->all();
                $depend_list = ArrayHelper::getColumn($depend_list, "depend_id");
                $model->depend_ids = $depend_list;

                $area_list = AreaSkillLink::find()
                    ->andWhere(["skill_id" => $model->id])
                    ->asArray()->all();
                $area_list = ArrayHelper::getColumn($area_list, "area_id");
                $model->area_ids = $area_list;

                $transaction->commit(); 
                return $this->render('config-save', [
                    'model'        => $model,
                    'typeArr'      => EntitySkill::$type_arr,
                    'dependSkills' => $skill_list,
                    'areaDict'     => $area_dict,
                    'id'           => $id,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnException($e);
        }
    }

    public function actionConfigDelete()
    {
        try {
            $transaction   = Yii::$app->db->beginTransaction();
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $ids_str = explode(',',$ids);
            $query = Skill::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                // todo 优化成批量删除的模式
                // 删除关联area_link
                AreaSkillLink::deleteAll(
                    "skill_id = :skill_id",
                    [":skill_id" => $model->id]
                );
                // 删除关联depend_link
                DependSkillLink::deleteAll(
                    "skill_id = :skill_id",
                    [":skill_id" => $model->id]
                );
                // 删除关联talent_link
                TalentSkillLink::deleteAll(
                    "skill_id = :skill_id",
                    [":skill_id" => $model->id]
                );
                
                // 删除技能本身
                $model->checkAndDelEntity();
            }
            $transaction->commit(); 
            $code = Error::ERR_OK;
            return $this->packageJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            $transaction->rollBack(); 
            return $this->returnexception($e);
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
                if (is_null($task_model->getTaskByEntityId(Area::FIELD_KNOWLEDGE, $obj->id))) {
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
