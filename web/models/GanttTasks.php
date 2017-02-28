<?Php

namespace app\models;

use Yii;
use app\models\Events;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class GanttTasks extends BaseActiveRecord
{
    const LEVEL_PROJECT = 0;
    const LEVEL_PLAN    = 1;
    const LEVEL_TASK    = 2;

    const TABLE_NAME    = "gantt_tasks";

    public static $level_arr = [
        self::LEVEL_PROJECT => "project",
        self::LEVEL_PLAN    => "plan",
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

    public function getInitTree()
    {
        $p_query = self::find()
            ->select("id, text")
            ->andFilterWhere(["$task_t.del" => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$task_t.user_id" => $this->user_id])
            ->andFilterWhere(["$task_t.type" => self::LEVEL_PLAN]);
        $p_result = $p_query->asArray()->all(); 

        $process_t = Process::tableName();
        $task_t = self::tableName();
        $t_query = self::find()
            ->select("$task_t.*, $process_t.task_id")
            ->leftJoin($process_t, "$process_t.task_id = $task_t.id")
            ->andFilterWhere(["$task_t.del" => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$task_t.user_id" => $this->user_id])
            ->andFilterWhere(["$task_t.type" => self::LEVEL_TASK]);
        $t_result = $t_query->asArray()->all();
        $t_list = ArrayHelper::getColumn($t_result, "id");

        $t_merge_arr = [];
        foreach ($t_result as &$one) {
            if (!is_null($one['task_id'])) {
                $one['child'] = 1;
            }
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

    public function checkAndChangeDuration()
    {
        if (empty($this->duration)) {
            $events_list = Events::find()
                ->andWhere(['task_id' => $this->id])
                ->select("start_date, end_date")
                ->asArray()->all();
            $min_start = "";
            $max_end = "";
            foreach ($events_list as $one) {
                $start = date("Y-m-d", strtotime($one['start_date']));
                $end   = date("Y-m-d", strtotime($one['end_date']));
                if (empty($min_start) || ($min_start > $start)) {
                    $min_start = $start;
                }
                if (empty($max_end) || ($max_end < $end)) {
                    $max_end = $end;
                }
            }
            $days = \DateUtil::daysBetween($min_start, $max_end);
            $duration = $days + 1;
            $this->duration = $duration;
            $this->start_date = $min_start;
            $this->modelValidSave();
        }
        return true;
    }
}
