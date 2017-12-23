<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class DailyScheduler extends BaseActiveRecord
{
    const TABLE_NAME    = "daily_scheduler";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'daily_id' => '作息id',
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
        $self_t = self::tableName();
        $query = self::find()
            ->orderBy("$self_t.ctime");
        $query->andFilterWhere(["$self_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$self_t.daily_id" => $this->daily_id]);
        return $query;
    }
}
