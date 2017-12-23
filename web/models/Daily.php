<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Daily extends BaseActiveRecord
{
    const TABLE_NAME    = "daily";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'name'     => '名称',
            'ctime'    => '创建时间',
            'utime'    => '更新时间',
        ];
    }

    public function rules()
    {
        return [
        ];
    }

    public function getQuery()
    {
        $p_s_t = self::tableName();
        $query = self::find()
            ->orderBy("$p_s_t.ctime");
        $query->andFilterWhere(["$p_s_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$p_s_t.name" => $this->name]);
        return $query;
    }
}
