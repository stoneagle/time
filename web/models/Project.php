<?Php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Action;
use app\models\Config;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Project extends BaseActiveRecord
{
    const PROGRESS_START = 0;
    const PROGRESS_END   = 1;
    const LEVEL_PROJECT  = 0;
    const LEVEL_TASK     = 1;
    const LEVEL_ACTION   = 2;

    const TABLE_NAME    = "project";

    // 用来额外存储process的id
    public $process_id = null;

    public static $level_arr = [
        self::LEVEL_PROJECT => "project",
        self::LEVEL_TASK    => "task",
        self::LEVEL_ACTION  => "action",
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
        $project_t = self::tableName();
        $query = self::find()
            ->orderBy("$project_t.ctime");
        $query->andFilterWhere(["$project_t.del"     => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$project_t.user_id" => $this->user_id]);
        return $query;
    }

    public static function getMaxId()
    {
        $p_id = Project::find()->select("max(id) as max_id")->asArray()->one();
        $t_id = Task::find()->select("max(id) as max_id")->asArray()->one();
        $a_id = Action::find()->select("max(id) as max_id")->asArray()->one();
        $p_id = $p_id["max_id"];
        $t_id = $t_id["max_id"];
        $a_id = $a_id["max_id"];
        return max($p_id, $t_id, $a_id) + 1;
    }

    /* public function checkAndChangeDuration() */
    /* { */
    /*     if (empty($this->process_id)) { */
    /*         throw new \Exception("无法获取行动列表", Error::ERR_GANTT_TASKS_DURATION_CHANGE); */
    /*     } */
    /*     $events_list = Events::find() */
    /*         ->andWhere(['process_id' => $this->process_id]) */
    /*         ->select("start_date") */
    /*         ->asArray()->all(); */
    /*     $events_t = Events::tableName(); */
    /*     $process_t = Process::tableName(); */
    /*     $process_list = Process::find() */
    /*         ->leftJoin($events_t, "$events_t.process_id = $process_t.id") */
    /*         ->andWhere(["$process_t.task_id" => $this->id]) */
    /*         ->select("max($events_t.end_date) as end_date") */
    /*         ->asArray()->one(); */
    /*     $max_end = $process_list['end_date']; */

    /*     $min_start = ""; */
    /*     foreach ($events_list as $one) { */
    /*         $start = date("Y-m-d", strtotime($one['start_date'])); */
    /*         $end   = date("Y-m-d", strtotime($one['end_date'])); */
    /*         if (empty($min_start) || ($min_start > $start)) { */
    /*             $min_start = $start; */
    /*         } */
    /*     } */

    /*     $days = \DateUtil::daysBetween($min_start, $max_end); */
    /*     $duration = $days + 1; */
    /*     $this->duration = $duration; */
    /*     $this->start_date = $min_start; */
    /*     $result = $this->modelValidSave(); */
    /*     return true; */
    /* } */
}
