<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class KnowledgeArea extends BaseEntity
{
    const TABLE_NAME    = "knowledge_area";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['parent', 'field'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getQuery()
    {
        $origin_t = "origin";
        $area_t = self::tableName();
        $query = self::find()
            ->select("$origin_t.*, $area_t.id as children_id")
            ->from("$area_t as $origin_t")
            ->andFilterWhere(["$origin_t.area_id" => $this->area_id])
            ->andFilterWhere(["$origin_t.parent"  => $this->parent])
            ->andFilterWhere(["$origin_t.del"     => $this->del])
            ->andFilterWhere(["$origin_t.id"     => $this->id])
            ->leftJoin($area_t, "$area_t.parent = $origin_t.id");
        return $query;
    }

    public function getAreaTreeArr()
    {
        $query = $this->getQuery();
        $result = $query
            ->orderBy("level desc")
            ->asArray()->all();
        $result = ArrayHelper::index($result, "id");
        foreach ($result as $index => $one) {
            // todo 以后修改为该领域下已投资时间
            $result[$index]["size"] = 500;
            if ($one["parent"] != 0) {
                $result[$one["parent"]]["children"][] = $result[$index];
                unset($result[$index]);
            } 
        }
        return array_values($result);
    }

    public function getAreaLeafDict($multi_flag = true, $type = self::DICT_TYPE_MAP)
    {
        $area_t = self::tableName();
        $area_list = $this->getQuery()
            ->andWhere(["$area_t.id" => null])
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
        $area_dict = [];
        foreach ($parent_list as $one) {
            switch ($type) {
                case self::DICT_TYPE_MAP :
                    $area_dict[$one["text"]] = $one["children"];
                    break;
                case self::DICT_TYPE_ARR :
                    foreach ($one["children"] as $cid => $cname) {
                        $area_dict[$one["text"]][] = [
                            'id' => $cid,
                            'name' => $cname,
                        ];
                    }
                    break;
            }
        }
        if (!$multi_flag) {
            $ret = [];
            foreach ($area_dict as $parent_name => $area_arr) {
                $ret = $entity_dict + $area_arr;
            }
        } else {
            $ret = $area_dict;
        }
        return $ret;
    }
}
