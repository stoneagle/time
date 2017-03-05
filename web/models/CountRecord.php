<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class CountRecord extends BaseActiveRecord
{
    const STATUS_EXEC   = 1;
    const STATUS_PAUSE  = 2;
    const STATUS_CANCEL = 3;
    const STATUS_FINISH = 4;

    const TABLE_NAME    = "count_record";

    public static $status_arr = [
        self::STATUS_EXEC   => "执行",
        self::STATUS_PAUSE  => "中止",
        self::STATUS_CANCEL => "撤销",
        self::STATUS_FINISH => "结束",
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
        $task_t = self::tableName();
        $query = self::find()
            ->andFilterWhere(["$task_t.user_id" => $this->user_id]);
        return $query;
    }
}
