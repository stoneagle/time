<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "process".
 *
 * @property integer $id
 * @property integer $plan_num
 * @property string $text
 * @property integer $user_id
 * @property integer $task_id
 * @property string $ctime
 * @property string $utime
 */
class Process extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'process';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plan_num', 'text', 'user_id', 'task_id'], 'required'],
            [['plan_num', 'user_id', 'task_id'], 'integer', "min" => 1],
            [['ctime', 'utime'], 'safe'],
            [['text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'plan_num' => '计划时间颗粒',
            'text'     => '内容',
            'user_id'  => '所属用户',
            'task_id'  => '所属任务',
            'ctime'    => '创建时间',
            'utime'    => '更新时间',
        ];
    }

    public function getTreeNodeList()
    {
        $process_t = self::tableName();
        $process_query = self::find()
            ->select("id, text, plan_num")
            ->andFilterWhere(["$process_t.user_id" => $this->user_id])
            ->andFilterWhere(["$process_t.task_id" => $this->task_id]);
        $process_list = $process_query->asArray()->all();
        return $process_list;
    }
}
