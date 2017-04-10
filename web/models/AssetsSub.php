<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class AssetsSub extends BaseActiveRecord
{
    const TABLE_NAME    = "assets_sub";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['entity_id', 'obj_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'name'      => '实体名称',
            'desc'      => '简单描述',
            'obj_id'    => '所属领域对象',
            'entity_id' => '所属实体',
            'ctime'     => '创建时间',
            'utime'     => '更新时间',
        ];
    }

    public function getQuery()
    {
        $sub_t = self::tableName();
        $entity_t = AssetsEntity::tableName();
        $query = self::find()
            ->select("$sub_t.*, $entity_t.name as entity_name")
            ->leftJoin($entity_t, "$entity_t.id = $sub_t.entity_id")
            ->andFilterWhere(["obj_id" => $this->obj_id]);
        return $query;
    }

    public static function getDict($obj_id)
    {
        $assets_sub_arr = self::find()
            ->andWhere(["obj_id" => $obj_id])
            ->asArray()->all();
        return ArrayHelper::map($assets_sub_arr, 'id', 'name');
    }
}
