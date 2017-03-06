<?Php

namespace app\models;

use Yii;
use app\models\Action;
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
}
