<?php

namespace app\models;

use Yii;
use app\models\Task;
use yii\base\Exception;

/**
 * This is the model class for table "events".
 *
 * @property integer $id
 * @property string $start_date
 * @property string $end_date
 * @property string $text
 * @property integer $user_id
 * @property string $ctime
 * @property string $utime
 */
class Action extends BaseActiveRecord
{
    public $join_project_id;
    public $task_name;
    public $entity_name;

    CONST STATUS_INIT = 0;
    CONST STATUS_WAIT = 1;
    CONST STATUS_EXEC = 2;
    CONST STATUS_END  = 3;

    CONST LIST_EXEC = 1;
    CONST LIST_END  = 2;

    public static $status_arr = [
        self::STATUS_INIT => "计划中",
        self::STATUS_WAIT => "等待中",
        self::STATUS_EXEC => "执行中",
        self::STATUS_END  => "已结束",
    ];

    public static $list_arr = [
        self::LIST_EXEC => [
            self::STATUS_WAIT,
            self::STATUS_EXEC
        ], 
        self::LIST_END => [
            self::STATUS_END
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'plan_time', 'status', 'task_id'], 'required'],
            [['plan_time'], 'integer', 'min' => 1, 'max' => 5],
            [['start_date', 'end_date', 'ctime', 'utime'], 'safe'],
            [['task_id', 'status', 'duration', 'user_id', 'status'], 'integer'],
            [['text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'text'            => '内容',
            'plan_time'       => '计划番茄',
            'status'          => '状态',
            'task_id'         => '所属任务',
            'start_date'      => '开始时间',
            'end_date'        => '结束时间',
            'ctime'           => '创建时间',
            'utime'           => '更新时间',
        ];
    }

    /* public function getTask() */
    /* { */
    /*     return $this->hasOne(Task::className(), ['id' => 'task_id']); */ 
    /* } */


    public function getQuery($entity_detail=False)
    {
        $action_t  = self::tableName();
        $task_t    = Task::tableName();
        $project_t = Project::tableName();
        $target_t  = Target::tableName();
        $query     = self::find()
            ->leftJoin($task_t, "$task_t.id = $action_t.task_id")
            ->leftJoin($project_t, "$project_t.id = $task_t.parent")
            ->leftJoin($target_t, "$target_t.id = $project_t.target_id")
            ;

        // 获取entity详情
        if ($entity_detail) {
            $project = Project::find()
                ->select("$target_t.field_id as join_field_id")
                ->leftJoin($target_t, "$target_t.id = $project_t.target_id")
                ->andFilterWhere(["$project_t.id" => $this->join_project_id])
                ->one();
            if (!is_null($project)) {
                switch($project->join_field_id) {
                    case Area::FIELD_CULTURE :
                        $entity_t = EntityWork::tableName();
                        break;
                    case Area::FIELD_KNOWLEDGE :
                        $entity_t     = EntitySkill::tableName();
                        break;
                    case Area::FIELD_CHANLLEGE :
                        $entity_t = EntityQuest::tableName();
                        break;
                    case Area::FIELD_SOCIAL :
                        $entity_t = EntityCircle::tableName();
                        break;
                    case Area::FIELD_WEALTH :
                        $entity_t = EntityAsset::tableName();
                        break;
                    case Area::FIELD_GENERAL :
                        $entity_t = EntityLife::tableName();
                        break;
                }
                $query = $query
                    ->leftJoin($entity_t, "$task_t.entity_id = $entity_t.id")
                    ->select("$action_t.*, $target_t.field_id, $target_t.priority_id, $task_t.text as task_name, $entity_t.name as entity_name");
            } else {
                $query = $query->select("$action_t.*, $target_t.field_id, $target_t.priority_id, $task_t.text as task_name");
            }
        } else {
            $query = $query->select("$action_t.*, $target_t.field_id, $target_t.priority_id, $task_t.text as task_name");
        }

        $query->andFilterWhere(["$action_t.status"     => $this->status]);
        $query->andFilterWhere(["$action_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$action_t.task_id" => $this->task_id]);
        $query->andFilterWhere(["$project_t.id" => $this->join_project_id]);
        $query->andFilterWhere([">=", "$action_t.start_date" , $this->start_date]);
        return $query;
    }

    public function afterSave($insert, $changedAttributes)
    {
        // 更新task的start_date
        $task = Task::find()
            ->andWhere(["id" => $this->task_id])
            ->one();
        if (strtotime($this->start_date) < strtotime($task->start_date)) {
            $task->start_date = $this->start_date;
            $task->modelValidSave();
        }
        // 更新project的start_date
        $project = Project::find()
            ->andWhere(["id" => $task->parent])
            ->one();
        if (strtotime($this->start_date) < strtotime($project->start_date)) {
            $project->start_date = $this->start_date;
            $project->modelValidSave();
        }
        return true;
    }
}
