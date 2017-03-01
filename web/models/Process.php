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
    CONST FINISH_NO    = 0;
    CONST FINISH_TRUE  = 1;
    CONST FINISH_FLAG  = "finish";

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

    private function getFinishDateRange()
    {
        $w = date("w", strtotime($date)); 
        $d = $w ? $w - $first : 6;
        $now_start = date("Y-m-d", strtotime("$date -".$d." days")); 
        return $now_start;
    }

    public function getTreeNodeList()
    {
        $process_t = self::tableName();
        $query = self::find()
            ->select("id, text, plan_num")
            ->andFilterWhere(["$process_t.user_id" => $this->user_id])
            ->andFilterWhere(["$process_t.task_id" => $this->task_id])
            ->andFilterWhere([
                "or",
                [
                    "and", 
                    ["$process_t.finish" => self::FINISH_TRUE], 
                    [ '>=', "$process_t.ctime", $this->getFinishDateRange() ]
                ],
                ["$process_t.finish" => self::FINISH_NO]
            ]);
        $list = $query->asArray()->all();
        return $list;
    }

    public function getQuery()
    {
        $process_t = self::tableName();
        $query = self::find()
            ->orderBy("$task_t.ctime");
        $query->andFilterWhere(["$task_t.user_id" => $this->user_id]);
        $query->andFilterWhere([
            "or",
            [
                "and", 
                ["$process_t.finish" => self::FINISH_TRUE], 
                [ '>=', "$process_t.ctime", $this->getFinishDateRange() ]
            ],
            ["$process_t.finish" => self::FINISH_NO]
        ]);
        return $query;
    }
}
