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

    // 获取每个用户执行或暂停的最新一项记录
    public function getOne()
    {
        $cr_t  = self::tableName();
        $t_t   = Task::tableName();
        $model = self::find()
            ->select("$cr_t.*, $t_t.text")
            ->leftJoin($t_t, "$cr_t.task_id = $t_t.id")
            ->andWhere([
                "$cr_t.status" => [
                    self::STATUS_EXEC,
                    self::STATUS_PAUSE,
                ]
            ])
            ->andWhere(["$cr_t.user_id" => $this->user_id])
            ->andFilterWhere(["$cr_t.id" => $this->id])
            ->orderBy("$cr_t.id desc")
            ->asArray()->one();
        return $model;
    }
}
