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
            [['assets_id'], 'integer'],
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
            'assets_id' => '所属资产',
            'ctime'     => '创建时间',
            'utime'     => '更新时间',
        ];
    }

    public function getQuery()
    {
        $entity_t = self::tableName();
        $query = self::find()
            ->andFilterWhere(["assets_id" => $this->assets_id]);
        return $query;
    }

    public static function getDict($assets_id)
    {
        $assets_sub_arr = self::find()
            ->andWhere(["assets_id" => $assets_id])
            ->asArray()->all();
        return ArrayHelper::map($assets_sub_arr, 'id', 'name');
    }
}
