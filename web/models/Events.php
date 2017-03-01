<?php

namespace app\models;

use Yii;
use app\models\Process;
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
class Events extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events';
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
            'process_id' => '所属过程',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $process_t = Process::tableName();
        $events_t = self::tableName();
        $query = self::find()
            ->select("
                $events_t.*, 
                $process_t.text as process_name,
                $process_t.finish
                ")
            ->leftJoin($process_t, "$process_t.id = $events_t.process_id");
        return $query;
    }
}
