<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class TargetEntityLink extends BaseActiveRecord 
{
    const TABLE_NAME    = "target_entity_link";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['target_id', 'entity_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'target_id' => '目标ID',
            'entity_id' => '实体ID',
            'ctime'     => '创建时间',
            'utime'     => '更新时间',
        ];
    }

    public function getQuery()
    {
        $query = self::find();
        return $query;
    }

    public static function getEntityArrs($target_id)
    {
        $arr = self::find()
            ->select("entity_id")
            ->andWhere(["target_id" => $target_id])
            ->asArray()->all();
        return ArrayHelper::getColumn($arr, "entity_id");
    }
}
