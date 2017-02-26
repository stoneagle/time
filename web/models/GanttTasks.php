<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class GanttTasks extends BaseActiveRecord
{
    const TABLE_NAME = "gantt_tasks";

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
        $task_t = self::tableName();
        $origin_t = "origin";
        $query = self::find()
            ->from([self::tableName(). " as {$origin_t}"])
            ->select("
                $origin_t.*,
                $task_t.parent as f_parent,
                $task_t.field_id as f_field
                ")
            ->orderBy("$task_t.ctime");
        $query->leftJoin($task_t, "{$origin_t}.parent = {$task_t}.id");
        $query->andFilterWhere(["$origin_t.del" => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$origin_t.user_id" => $this->user_id]);
        return $query;
    }
}
