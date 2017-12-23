<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Area extends BaseActiveRecord 
{
    const TABLE_NAME    = "area";

    CONST FIELD_KNOWLEDGE = 1;
    CONST FIELD_WEALTH    = 2;
    CONST FIELD_CULTURE   = 3;
    CONST FIELD_SOCIAL    = 4;
    CONST FIELD_CHANLLEGE = 5;
    CONST FIELD_GENERAL   = 6;

    public static $field_arr = [
        self::FIELD_KNOWLEDGE => "知识",
        self::FIELD_WEALTH    => "财富",
        self::FIELD_CULTURE   => "文化",
        self::FIELD_SOCIAL    => "社交",
        self::FIELD_CHANLLEGE => "挑战",
        self::FIELD_GENERAL   => "通用",
    ];

    public static $field_en_arr = [
        self::FIELD_KNOWLEDGE => "knowledge",
        self::FIELD_WEALTH    => "wealth",
        self::FIELD_CULTURE   => "culture",
        self::FIELD_SOCIAL    => "social",
        self::FIELD_CHANLLEGE => "chanllege",
        self::FIELD_GENERAL   => "general",
    ];

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['parent', 'field_id', 'level', 'del'], 'integer'],
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
            ->andFilterWhere(["$origin_t.field_id" => $this->field_id])
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

    public function getAreaLeafDict($field_id, $multi_flag = true, $type = Constants::DICT_TYPE_MAP)
    {
        $father_t = "father";
        $middle_t = "middle";
        $son_t = self::tableName();
        $area_list = self::find()
            ->select("$father_t.name as parent_name, $middle_t.id, $middle_t.name")
            ->from([$middle_t => self::tableName()])
            ->leftJoin($son_t, "$son_t.parent = $middle_t.id")
            ->leftJoin("$son_t as $father_t", "$father_t.id = $middle_t.parent")
            ->andWhere(["$middle_t.field_id" => $field_id])
            ->andWhere(["$son_t.id" => null])
            ->andWhere(["$middle_t.del" => Constants::SOFT_DEL_NO])
            ->andWhere(["NOT", ["$middle_t.parent" => 0]])
            ->asArray()->all();
        $area_dict = [];
        foreach ($area_list as $one) {
            switch ($type) {
                case Constants::DICT_TYPE_MAP :
                    if ($multi_flag) {
                        $area_dict[$one["parent_name"]][$one["id"]] = $one["name"];
                    } else {
                        $area_dict[$one["id"]] = $one["name"];
                    }
                    break;
                case Constants::DICT_TYPE_ARR :
                    if ($multi_flag) {
                        $area_dict[$one['parent_name']][] = [
                            'id'   => $one['id'],
                            'name' => $one['name'],
                        ];
                    } else {
                        $area_dict[] = [
                            'id'   => $one['id'],
                            'name' => $one['name'],
                        ];
                    }
                    break;
            }
        }
        return $area_dict;
    }
}
