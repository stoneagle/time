<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Country extends BaseActiveRecord
{
    const TABLE_NAME    = "country";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name', 'en_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'name'    => '名称',
            'en_name' => '英文名称',
            'ctime'   => '创建时间',
            'utime'   => '更新时间',
        ];
    }

    public function getQuery()
    {
        $country_t = self::tableName();
        $query = self::find();
        return $query;
    }

    public static function getDict()
    {
        $country_arr = self::find()
            ->select("name, id")
            ->asArray()->all();
        $country_dict = ArrayHelper::map($country_arr, "id", "name");
        return $country_dict;
    }
}
