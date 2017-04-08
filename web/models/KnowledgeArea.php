<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class KnowledgeArea extends BaseActiveRecord
{
    const TABLE_NAME    = "knowledge_area";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
            [['parent', 'field'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
}
