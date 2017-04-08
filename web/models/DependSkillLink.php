<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class DependSkillLink extends BaseActiveRecord
{
    const TABLE_NAME    = "depend_skill_link";

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
            [['depend_id', 'skill_id'], 'integer', 'min' => 1],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function getQuery()
    {
        $task_t = self::tableName();
        $query = self::find();
        return $query;
    }
}
