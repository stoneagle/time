<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class FieldObjEntityLink extends BaseActiveRecord 
{
    const TABLE_NAME    = "field_obj_entity_link";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['obj_id', 'entity_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            //[['project_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'obj_id'     => '领域对象ID',
            'entity_id'  => '相关实体ID',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $query = self::find();
        return $query;
    }
}
