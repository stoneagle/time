<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class BusinessAssets extends BaseActiveRecord
{
    const TYPE_STREAM = 1;
    const TYPE_TRADE  = 2;
    const TYPE_BUBBLE = 3;

    const TABLE_NAME    = "business_assets";

    public static $type_arr = [
        self::TYPE_STREAM => "现金流",
        self::TYPE_TRADE  => "交易",
        self::TYPE_BUBBLE => "泡沫",
    ];

    public static $type_access_arr = [
        self::TYPE_STREAM => "日期间隔",
        self::TYPE_TRADE  => "次数",
        self::TYPE_BUBBLE => "人力",
    ];

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
            [['type_id', 'value', 'time'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['position'], 'string', 'max' => 255],
        ];
    }

    public function getQuery()
    {
        $task_t = self::tableName();
        $query = self::find()
            ->andFilterWhere(["$task_t.user_id" => $this->user_id]);
        return $query;
    }
}
