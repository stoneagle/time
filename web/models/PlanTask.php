<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class PlanTask extends BaseActiveRecord
{
    const TABLE_NAME    = "plan_task";

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
        $p_t_t = self::tableName();
        $query = self::find()
            ->orderBy("$p_t_t.ctime");
        $query->andFilterWhere(["$p_t_t.user_id" => $this->user_id]);
        return $query;
    }
}
