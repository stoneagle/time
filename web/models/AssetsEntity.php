<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class AssetsEntity extends BaseActiveRecord
{
    const TYPE_STREAM = 1;
    const TYPE_TRADE  = 2;
    const TYPE_BUBBLE = 3;

    const TABLE_NAME    = "assets_entity";

    public static $type_arr = [
        self::TYPE_STREAM => "现金流",
        self::TYPE_TRADE  => "交易",
        self::TYPE_BUBBLE => "泡沫",
    ];

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['type_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'name'    => '实体名称',
            'type_id' => '所属类别',
            'ctime'   => '创建时间',
            'utime'   => '更新时间',
        ];
    }

    public function getQuery()
    {
        $entity_t = self::tableName();
        $query = self::find();
        return $query;
    }
}
