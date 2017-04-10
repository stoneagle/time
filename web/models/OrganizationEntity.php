<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class OrganizationEntity extends BaseEntity
{
    const TABLE_NAME    = "organization_entity";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['parent'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'name'   => '实体名称',
            'desc'   => '简单描述',
            'parent' => '所属实体',
            'ctime'  => '创建时间',
            'utime'  => '更新时间',
        ];
    }

    public function getQuery()
    {
        return parent::getQuery();
    }

    public static function getParentDict()
    {
        return parent::getParentDict();
    }

    public static function getChildDict($type = self::DICT_TYPE_ARR)
    {
        return parent::getChildDict($type);
    }
}
