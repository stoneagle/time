<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class EntityBase extends BaseActiveRecord
{
    public $user_id;

    // 根据field_id获取entity列表
    public function getEntityList($field_id, $entity_ids_arr)
    {
        switch ($field_id) {
            case Area::FIELD_CULTURE :
                $entity_t = EntityWork::tableName();
                $area_t   = Area::tableName();
                $query    = EntityWork::find()
                    ->select("$entity_t.id, $entity_t.name, $area_t.name as area_name")
                    ->leftJoin($area_t, "$area_t.id = $entity_t.area_id");
                break;
            case Area::FIELD_KNOWLEDGE :
                $entity_t     = EntitySkill::tableName();
                $entity_mid_t = AreaSkillLink::tableName();
                $area_t       = Area::tableName();
                $query        = AreaSkillLink::find()
                    ->select("$entity_t.id, $entity_t.name, $area_t.name as area_name")
                    ->leftJoin($entity_t, "$entity_mid_t.skill_id = $entity_t.id")
                    ->leftJoin($area_t, "$area_t.id               = $entity_mid_t.area_id");
                break;
            case Area::FIELD_CHANLLEGE :
                $entity_t = EntityQuest::tableName();
                $area_t   = Area::tableName();
                $query    = EntityQuest::find()
                    ->select("$entity_t.id, $entity_t.name, $area_t.name as area_name")
                    ->leftJoin($area_t, "$area_t.id = $entity_t.area_id");
                break;
            case Area::FIELD_SOCIAL :
                $entity_t = EntityCircle::tableName();
                $area_t   = Area::tableName();
                $query    = EntityCircle::find()
                    ->select("$entity_t.id, $entity_t.name, $area_t.name as area_name")
                    ->leftJoin($area_t, "$area_t.id = $entity_t.area_id");
                break;
            case Area::FIELD_WEALTH :
                $entity_t = EntityAsset::tableName();
                $area_t   = Area::tableName();
                $query    = EntityAsset::find()
                    ->select("$entity_t.id, $entity_t.name, $area_t.name as area_name")
                    ->leftJoin($area_t, "$area_t.id = $entity_t.area_id");
                break;
            case Area::FIELD_GENERAL :
                $entity_t = EntityLife::tableName();
                $query    = EntityLife::find()
                    ->select("$entity_t.id, $entity_t.name");
                break;
        }
        if (!empty($entity_ids_arr)) {
            $query->andWhere(["$entity_t.id" => $entity_ids_arr]);
        }
        // yii2无法赋予常量
        $list = $query->asArray()->all();
        if ($field_id == Area::FIELD_GENERAL) {
            foreach ($list as $index => $one) {
                $list[$index]["area_name"] = "日常生活";
            }
        }
        return $list;
    }

    public function getEntityDict($field_id, $type = Constants::DICT_TYPE_ARR, $entity_ids_arr = [], $multi_flag = true)
    {
        $list = $this->getEntityList($field_id, $entity_ids_arr);
        $dict = \ArrDict::getDictByType($type, $list, $multi_flag, "area_name");
        return $dict;
    }

    public function checkAndDelEntity()
    {
        $target_entity_link_t = TargetEntityLink::tableName();
        $target_t             = Target::tableName();
        $result               = TargetEntityLink::find()
            ->select("$target_t.id")
            ->leftJoin($target_t, "$target_t.id = $target_entity_link_t.target_id")
            ->andWhere(["$target_entity_link_t.entity_id" => $this->id])
            ->andWhere(["$target_t.field_id" => static::ENTITY_TYPE])
            ->asArray()->one();
        if (is_null($result)) {
            $this->delete();
        } else {
            throw new \Exception ("该实体已使用，禁止删除", Error::ERR_DEL);
        }
        return true;
    }
}
