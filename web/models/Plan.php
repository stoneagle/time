<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Plan extends BaseActiveRecord
{
    const TABLE_NAME    = "plan";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'daily_id'   => '作息模板',
            'user_id'    => '所属用户',
            'from_date' => '开始日期',
            'to_date'    => '完成日期',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function rules()
    {
        return [
            [['id', 'daily_id', 'user_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['from_date', 'to_date'], 'string', 'max' => 255],
        ];
    }

    public function getQuery()
    {
        $self_t = self::tableName();
        $query = self::find()
            ->orderBy("$self_t.ctime");
        $query->andFilterWhere(["$self_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$self_t.from_date" => $this->from_date]);
        $query->andFilterWhere(["$self_t.to_date" => $this->to_date]);
        return $query;
    }

    public function getCheckListQuery()
    {
        $action_t  = Action::tableName();
        $task_t  = Task::tableName();
        $project_t = Project::tableName();
        $from_date = $this->from_date . " 00:00:00";
        $to_date = $this->to_date . " 00:00:00";
        $query = Action::find() 
            ->select("$project_t.id, SUM($action_t.exec_time) as sum_time")
            ->leftJoin($task_t, "$task_t.id = $action_t.task_id")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            ->andFilterWhere(["$project_t.user_id"  => $this->user_id])
            ->andFilterWhere([">=", "$action_t.start_date" , $from_date])
            ->andFilterWhere(["<=", "$action_t.end_date" , $to_date])
            ->groupBy("$project_t.id")
            ->orderBy("$project_t.id desc");
        return $query;
    }
}
