<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class EntityLife extends EntityBase
{
    const TABLE_NAME  = "entity_life";
    const ENTITY_TYPE = Area::FIELD_GENERAL;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],
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
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $entity_t = self::tableName();
        $query    = self::find()
            ->select("$entity_t.*")
            ->andFilterWhere(["$entity_t.name"    => $this->name]);
        return $query;
    }
}
