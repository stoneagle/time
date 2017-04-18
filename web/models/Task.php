<?Php

namespace app\models;

use Yii;
use app\models\Action;
use app\models\Project;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Task extends BaseActiveRecord
{
    const PROGRESS_START = 0;
    const PROGRESS_END   = 1;

    const TABLE_NAME    = "task";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['parent', 'duration', 'progress', 'user_id', 'entity_id', 'del'], 'integer'],
            [['start_date', 'ctime', 'utime'], 'safe'],
            [['text'], 'string', 'max' => 255],
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
        return $query;
    }

    public function getTaskWithActionQuery()
    {
        $task_t   = self::tableName();
        $action_t = Action::tableName();
        $query    = $this->getQuery()
            ->select("$task_t.*")
            ->leftJoin($action_t, "$action_t.task_id = $task_t.id")
            ->orderBy("$action_t.start_date");
        return $query;
    }

    public function getPlanTask()
    {
        $task_t         = self::tableName();
        $project_t      = Project::tableName();
        $action_t       = Action::tableName();
        $plan_task_t    = PlanTask::tableName();
        $query          = $this->getQuery();
        $query->select("
            $task_t.id, $task_t.text, sum($action_t.plan_time) as sum_time, $task_t.progress, max($plan_task_t.week) as week
            ")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            ->leftJoin($action_t, "$action_t.task_id = $task_t.id")
            ->leftJoin($plan_task_t, "$plan_task_t.task_id = $task_t.id")
            ->groupBy("$task_t.id");
        return $query;
    }


    public function getTaskWithProjectText()
    {
        $action_t  = Action::tableName();
        $task_t    = self::tableName();
        $project_t = Project::tableName();
        $target_t  = Target::tableName();
        $query     = $this->getQuery()
            ->select([ "
            $task_t.id, $task_t.text, $task_t.entity_id, 
            $project_t.text as project_text,
            $project_t.target_id,
            $target_t.field_id,
            COUNT($action_t.id) as action_num,
            SUM(IF($action_t.status = ".Action::STATUS_END.", 1,0)) as exec_num
            " ])
            ->leftJoin($project_t, "$task_t.parent = $project_t.id")
            ->leftJoin($action_t, "$action_t.task_id = $task_t.id")
            ->leftJoin($target_t, "$target_t.id = $project_t.target_id")
            ->andWhere(["NOT", ["$task_t.progress" => 1]])
            ->orderby("$task_t.parent, $task_t.start_date")
            ->groupby("$task_t.id")
            ;
        return $query->asArray()->all();
    }

    public function getTaskByEntityId($field_id, $entity_id)
    {
        $task_t    = self::tableName();
        $project_t = Project::tableName();
        $query = $this->getQuery()
            ->leftJoin($project_t, "$project_t.id = $task_t.parent");
        $query->andWhere(["$project_t.field_id" => $field_id]);
        $query->andWhere(["$task_t.entity_id" => $entity_id]);
        return $query->asArray()->all();
    }
}
