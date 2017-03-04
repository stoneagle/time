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
            [['start_date', 'end_date', 'process_id', 'user_id'], 'required'],
            [['start_date', 'end_date', 'ctime', 'utime'], 'safe'],
            [['user_id', 'process_id', 'user_id'], 'integer'],
            [['text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'start_date' => '开始时间',
            'end_date'   => '结束时间',
            'text'       => '内容',
            'user_id'    => '所属用户',
            'type_id'    => '过程类别',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $task_t = Task::tableName();
        $action_t = self::tableName();
        $query = self::find()
            ->select("
                $action_t.*, 
                $task_t.text as task_name,
                $task_t.progress
                ")
            ->leftJoin($task_t, "$task_t.id = $action_t.task_id");
        return $query;
    }
}
