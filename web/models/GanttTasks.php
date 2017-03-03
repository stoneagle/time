<?Php

namespace app\models;

use Yii;
use app\models\Events;
use app\models\Process;
use app\models\Config;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class GanttTasks extends BaseActiveRecord
{
    const PROGRESS_START = 0;
    const PROGRESS_END   = 1;
    const LEVEL_PROJECT  = 0;
    const LEVEL_PLAN     = 1;
    const LEVEL_TASK     = 2;

    const TABLE_NAME    = "gantt_tasks";

    // 用来额外存储process的id
    public $process_id = null;

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
        $task_t = self::tableName();
        $origin_t = "origin";
        $p_query = self::find()
            ->select("$origin_t.id, $origin_t.text, $task_t.priority_id")
            ->from([$origin_t => $task_t])
            ->leftJoin($task_t, "$task_t.id = $origin_t.parent")
            ->andWhere(["<", "$origin_t.progress" , 1])
            ->andFilterWhere(["$origin_t.del"     => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$origin_t.user_id" => $this->user_id])
            ->andFilterWhere(["$origin_t.type"    => self::LEVEL_PLAN])
            ->orderBy("$task_t.priority_id, $origin_t.id");
        $p_result = $p_query->asArray()->all(); 

        $process_t = Process::tableName();
        $task_t = self::tableName();
        $t_query = self::find()
            ->select("$task_t.*, $process_t.task_id")
            ->leftJoin($process_t, "$process_t.task_id = $task_t.id")
            ->andWhere(["<", "$task_t.progress" , 1])
            ->andFilterWhere(["$task_t.del" => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$task_t.user_id" => $this->user_id])
            ->andFilterWhere(["$task_t.type" => self::LEVEL_TASK]);
        $t_result = $t_query->asArray()->all();
        $t_list = ArrayHelper::getColumn($t_result, "id");

        $config_model       = new Config;
        $config_model->type = Config::TYPE_PRIORITY;
        $priority_dict      = $config_model->getTypeDict();

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
                    $priority_name = $priority_dict[$f_one['priority_id']];
                    $f_one['text'] = "({$priority_name})".$f_one['text'];
                    $f_one['item'] = $one;
                    $f_one['open'] = true;
                }
            }
        }
        return $p_result;
    }

    public function checkAndChangeDuration()
    {
        if (empty($this->process_id)) {
            throw new \Exception("无法获取行动列表", Error::ERR_GANTT_TASKS_DURATION_CHANGE);
        }
        $events_list = Events::find()
            ->andWhere(['process_id' => $this->process_id])
            ->select("start_date")
            ->asArray()->all();
        $events_t = Events::tableName();
        $process_t = Process::tableName();
        $process_list = Process::find()
            ->leftJoin($events_t, "$events_t.process_id = $process_t.id")
            ->andWhere(["$process_t.task_id" => $this->id])
            ->select("max($events_t.end_date) as end_date")
            ->asArray()->one();
        $max_end = $process_list['end_date'];

        $min_start = "";
        foreach ($events_list as $one) {
            $start = date("Y-m-d", strtotime($one['start_date']));
            $end   = date("Y-m-d", strtotime($one['end_date']));
            if (empty($min_start) || ($min_start > $start)) {
                $min_start = $start;
            }
        }

        $days = \DateUtil::daysBetween($min_start, $max_end);
        $duration = $days + 1;
        $this->duration = $duration;
        $this->start_date = $min_start;
        $result = $this->modelValidSave();
        return true;
    }
}
