<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class EntityQuest extends EntityBase 
{
    const TABLE_NAME  = "entity_quest";
    const ENTITY_TYPE = Area::FIELD_CHANLLEGE;
    public $area_name;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['area_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => '名称',
            'desc'       => '描述',
            'area_id'    => '所属领域',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $entity_t = self::tableName();
        $area_t   = Area::tableName();
        $query    = self::find()
            ->select("$entity_t.*, $area_t.name as area_name")
            ->leftJoin($area_t, "$area_t.id = $entity_t.area_id")
            ->andFilterWhere(["$entity_t.name"    => $this->name])
            ->andFilterWhere(["$entity_t.area_id" => $this->area_id]);
        return $query;
    }
}
