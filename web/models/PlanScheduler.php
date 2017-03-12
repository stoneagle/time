<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class PlanScheduler extends BaseActiveRecord
{
    const TABLE_NAME    = "plan_scheduler";

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
        ];
    }

    public function getQuery()
    {
        $p_s_t = self::tableName();
        $query = self::find()
            ->orderBy("$p_s_t.ctime");
        $query->andFilterWhere(["$p_s_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$p_s_t.week" => $this->week]);
        return $query;
    }
}
