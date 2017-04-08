<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class ArtEntity extends BaseActiveRecord
{
    const TABLE_NAME    = "art_entity";

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
        $art_t = self::tableName();
        $query = self::find();
        return $query;
    }

    public static function getParentDict()
    {
        $parent_arr = self::find()
            ->select("name, id")
            ->andWhere(["parent" => 0])
            ->asArray()->all();
        $parent_dict = ArrayHelper::map($parent_arr, "id", "name");
        $parent_dict[0] = "无隶属";
        ksort($parent_dict);
        return $parent_dict;
    }

    public static function getChildDict()
    {
        $parent_arr = self::find()
            ->select("name, id")
            ->andWhere(["NOT", ["parent" => 0]])
            ->asArray()->all();
        $parent_dict = ArrayHelper::map($parent_arr, "id", "name");
        return $parent_dict;
    }
}
