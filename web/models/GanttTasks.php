<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class GanttTasks extends BaseActiveRecord
{
    const LEVEL_PLAN    = 0;
    const LEVEL_PROJECT = 1;
    const LEVEL_TASK    = 2;

    const TABLE_NAME    = "gantt_tasks";

    public static $level_arr = [
        self::LEVEL_PLAN    => "plan",
        self::LEVEL_PROJECT => "project",
        self::LEVEL_TASK    => "task",
    ];

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
        $query = self::find()
            ->orderBy("$task_t.ctime");
        $query->andFilterWhere(["$task_t.del"     => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$task_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$task_t.type"    => $this->type]);
        return $query;
    }

    public function getTree()
    {
        $p_query = self::find()
            ->select("id, text")
            ->andFilterWhere(["$task_t.del" => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$task_t.user_id" => $this->user_id])
            ->andFilterWhere(["$task_t.type" => self::LEVEL_PROJECT]);
        $p_result = $p_query->asArray()->all(); 

        $t_query = self::find()
            ->select("id, text, parent")
            ->andFilterWhere(["$task_t.del" => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$task_t.user_id" => $this->user_id])
            ->andFilterWhere(["$task_t.type" => self::LEVEL_TASK]);
        $t_result = $t_query->asArray()->all();

        $t_merge_arr = [];
        foreach ($t_result as $one) {
            $t_merge_arr[$one['parent']][] = $one;
        }

        foreach ($t_merge_arr as $pid => $one) {
            foreach ($p_result as &$f_one) {
                if ($f_one['id'] == $pid) {
                    $f_one['item'] = $one;
                    $f_one['open'] = true;
                }
            }
        }
        return $p_result;
    }
}
