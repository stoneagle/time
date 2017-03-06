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
            [['text', 'type_id', 'plan_time', 'status', 'task_id'], 'required'],
            [['plan_time'], 'integer', 'min' => 1, 'max' => 5],
            [['task_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /* public function getTask() */
    /* { */
    /*     return $this->hasOne(Task::className(), ['id' => 'task_id']); */ 
    /* } */


    public function getQuery()
    {
        $action_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$action_t.status"     => $this->status]);
        $query->andFilterWhere(["$action_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$action_t.task_id" => $this->task_id]);
        $query->andFilterWhere([">=", "$action_t.start_date" , $this->start_date]);
        return $query;
    }
}
