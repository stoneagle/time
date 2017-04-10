<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\models\AssetsEntity;

class AssetsInfo extends BaseActiveRecord
{
    const TABLE_NAME    = "assets_info";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['obj_id', 'trade_num', 'head_count', 'time_span', 'value', 'income_flow'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['position'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'obj_id'      => '领域对象ID',
            'trade_num'   => '交易次数',
            'head_count'  => '人力资源',
            'time_span'   => '时间间隔',
            'value'       => '价值',
            'income_flow' => '现金流',
            'position'    => '位置',
            'ctime'       => '创建时间',
            'utime'       => '修改时间',
        ];
    }

    public function getQuery()
    {
        $assets_t = self::tableName();
        $field_t = FieldObj::tableName();
        $query = self::find()
            ->select("$assets_t.*")
            ->leftJoin($field_t, "$field_t.id = $assets_t.obj_id");
        return $query;
    }
}
