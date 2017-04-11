<?Php

namespace app\models;

use Yii;
use app\models\Action;
use app\models\Project;
use app\models\Config;
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

    public function getPlanTask()
    {
        $task_t         = self::tableName();
        $project_t      = Project::tableName();
        $action_t       = Action::tableName();
        $plan_task_t    = PlanTask::tableName();
        $query          = $this->getQuery();
        $query->select("
            $task_t.id, $task_t.text, $project_t.field_id, sum($action_t.plan_time) as sum_time, $task_t.progress, max($plan_task_t.week) as week
            ")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            ->leftJoin($action_t, "$action_t.task_id = $task_t.id")
            ->leftJoin($plan_task_t, "$plan_task_t.task_id = $task_t.id")
            ->groupBy("$task_t.id");
        return $query;
    }


    public function getTaskWithFieldAndPriorityList($field_dict, $priority_dict)
    {
        $query     = $this->getQuery();
        $task_t    = self::tableName();
        $project_t = Project::tableName();
        $query->leftJoin($project_t, "$task_t.parent = $project_t.id");
        $query->select("
            $task_t.id, $task_t.text, $project_t.priority_id,
            $project_t.field_id,$project_t.text as project_text
            ")->orderby("$project_t.priority_id, $task_t.ctime");
        $result = $query->asArray()->all();

        $task_list = [];
        foreach ($result as $one) {
            $priority_name = ArrayHelper::getValue($priority_dict, $one["priority_id"]); 
            $field_name = ArrayHelper::getValue($field_dict, $one["field_id"]); 
            $text = "[$priority_name]".$field_name."——".$one["project_text"]."——".$one["text"];
            $task_list[$one['id']] = [
                "text"      => $text,
                "task_name" => $one['text'],
                "field_id" => $one['field_id'],
            ];
        }
        return $task_list;
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

    public static function getTaskWithProject($task_id)
    {
        $task_t = self::tableName();
        $project_t = Project::tableName();
        //$field_obj_t = FieldObj::tableName();
        $result = self::find() 
            ->select("$project_t.obj_id, $project_t.field_id, $task_t.entity_id")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            //->leftJoin($field_obj_t, "$project_t.obj_id = $field_obj_t.id")
            ->andWhere(["$task_t.id" => $task_id])
            ->asArray()->one();
        return $result;
    }
}
