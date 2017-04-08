<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\models\AssetsEntity;

class Assets extends BaseActiveRecord
{
    const TABLE_NAME    = "assets";

    public static $type_access_arr = [
        AssetsEntity::TYPE_STREAM => "日期间隔",
        AssetsEntity::TYPE_TRADE  => "次数",
        AssetsEntity::TYPE_BUBBLE => "人力",
    ];

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['entity_id', 'value', 'time'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['position'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getQuery()
    {
        $assets_t = self::tableName();
        $entity_t = AssetsEntity::tableName();
        $query = self::find()
            ->select("$entity_t.type_id as type_id, $assets_t.*")
            ->andFilterWhere(["$assets_t.user_id" => $this->user_id])
            ->leftJoin($entity_t, "$entity_t.id = $assets_t.entity_id");
        return $query;
    }
}
