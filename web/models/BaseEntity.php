<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class BaseEntity extends BaseActiveRecord
{
    CONST DICT_TYPE_MAP = 1;
    CONST DICT_TYPE_ARR = 2;

    public function getQuery()
    {
        $entity_t = static::tableName();
        $query = static::find();
        return $query;

    }

    public static function getParentDict()
    {
        $parent_arr = static::find()
            ->select("name, id")
            ->andWhere(["parent" => 0])
            ->asArray()->all();
        $parent_dict = ArrayHelper::map($parent_arr, "id", "name");
        $parent_dict[0] = "无隶属";
        ksort($parent_dict);
        return $parent_dict;
    }

    public static function getChildDict($type = self::DICT_TYPE_MAP)
    {
        $origin_t = "origin";
        $self_t = static::tableName();
        $parent_arr = static::find()
            ->select("$origin_t.id, $origin_t.name, self.name as parent_name")
            ->from([$origin_t => $self_t])
            ->leftJoin($self_t, "$self_t.parent = $origin_t.id")
            ->leftJoin("$self_t as self", "self.id = $origin_t.parent")
            ->andWhere(["NOT", ["$origin_t.parent" => 0]])
            ->andWhere(["$self_t.id" => null])
            ->asArray()->all();
        foreach ($parent_arr as $one) {
            switch ($type) {
                case self::DICT_TYPE_MAP :
                    $dict[$one["parent_name"]][$one["id"]] = $one["name"];
                    break;
                case self::DICT_TYPE_ARR :
                    $dict[$one["parent_name"]][] = [
                        'id' => $one["id"],
                        'name' => $one["name"],
                    ];
                    break;
            }
        }
        return $dict;
    }
}
