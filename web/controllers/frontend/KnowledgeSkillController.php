<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\KnowledgeSkill;
use app\models\DependSkillLink;
use app\models\AreaSkillLink;
use app\models\TalentSkillLink;
use app\models\KnowledgeArea;
use app\models\Error;
use app\models\UserSkillLink;
use app\models\Constants;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class KnowledgeSkillController extends BaseController
{
    public function actionIndex()
    {
        $area_id     = Yii::$app->request->get('area_id', null);
        $model       = new KnowledgeSkill;
        $skill_t     = KnowledgeSkill::tableName();
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
            $left            = $type_skill_nums * KnowledgeSkill::SKILL_WIDTH;
            $top             = KnowledgeSkill::SKILL_INIT_HEIGHT 
                + floor($type_skill_nums/KnowledgeSkill::SKILL_WIDTH_NUM) * KnowledgeSkill::SKILL_HEIGHT
                + ($one["type_id"] - 1) * KnowledgeSkill::SKILL_TYPE_HEIGHT;
            
            $skill_list[] = [
                "id"               => (int)$one["id"],
                "title"            => $one["title"],
                "description"      => $one["description"],
                "rankDescriptions" => array_values(KnowledgeSkill::$default_level_desc),
                "links"            => [],
                "dependsOn"        => is_null($one["depend_ids"]) ? [] : explode(",", $one["depend_ids"]),
                "maxPoints"        => KnowledgeSkill::DEFAULT_LEVEL_MAX,
                "points"           => $one["user_level"],
                "stats"            => [],
                "margin_left"      => $left."px",
                "margin_top"       => $top."px",
            ];
        }

        return $this->render('index', [
            "skill_list" => json_encode($skill_list),
            "type_dict_arr"  => KnowledgeSkill::$type_arr,
        ]);
    }

    public function actionValid()
    {
        try {
            $model = new KnowledgeSkill();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionConfigIndex()
    {
        $model = new KnowledgeSkill();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('config-index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
            'typeArr'      => KnowledgeSkill::$type_arr,
        ]);
    }

    public function actionConfigCreate()
    {
        $model = new KnowledgeSkill();
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
                $skill_list = KnowledgeSkill::find()->select("id, title")->asArray()->all();
                $skill_list = ArrayHelper::map($skill_list,"id","title");

                $area = new KnowledgeArea;
                $area_t = KnowledgeArea::tableName();
                $area_list = $area->getQuery()
                    ->andWhere(["$area_t.id" => null])
                    ->andWhere(["origin.del" => Constants::SOFT_DEL_NO])
                    ->asArray()->all();
                $parent_arr = ArrayHelper::getColumn($area_list, "parent");
                $parent_list = KnowledgeArea::find()
                    ->select("id, name as text")
                    ->andWhere(["id" => array_unique($parent_arr)])
                    ->asArray()->all();
                $parent_list = ArrayHelper::index($parent_list, "id");
                foreach ($area_list as $one) {
                    $parent_list[$one["parent"]]["children"][(int)$one["id"]] = $one["name"];
                }
                foreach ($parent_list as $one) {
                    $area_dict[$one["text"]] = $one["children"];
                }

                $model->max_points = 4;
                $transaction->commit(); 
                return $this->render('config-save', [
                    'model'   => $model,
                    'typeArr' => KnowledgeSkill::$type_arr,
                    'dependSkills' => $skill_list,
                    'areaDict' => $area_dict,
                    'talents' => [],
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
            $model  = $this->findModel($id, KnowledgeSkill::class);
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
                $skill_list = KnowledgeSkill::find()->select("id, title")->asArray()->all();
                $skill_list = ArrayHelper::map($skill_list,"id","title");
                if (isset($skill_list[$model->id])) {
                    unset($skill_list[$model->id]);
                }

                $area = new KnowledgeArea;
                $area_t = KnowledgeArea::tableName();
                $area_list = $area->getQuery()
                    ->andWhere(["$area_t.id" => null])
                    ->andWhere(["origin.del" => Constants::SOFT_DEL_NO])
                    ->asArray()->all();
                $parent_arr = ArrayHelper::getColumn($area_list, "parent");
                $parent_list = KnowledgeArea::find()
                    ->select("id, name as text")
                    ->andWhere(["id" => array_unique($parent_arr)])
                    ->asArray()->all();
                $parent_list = ArrayHelper::index($parent_list, "id");
                foreach ($area_list as $one) {
                    $parent_list[$one["parent"]]["children"][(int)$one["id"]] = $one["name"];
                }
                foreach ($parent_list as $one) {
                    $area_dict[$one["text"]] = $one["children"];
                }

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
                    'typeArr'      => KnowledgeSkill::$type_arr,
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
            $query = KnowledgeSkill::find()->andWhere(['and',['in', 'id', $ids]]);
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
                $result = $model->delete();
                if (!$result) {
                    throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }

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
