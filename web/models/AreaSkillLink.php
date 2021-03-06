<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class AreaSkillLink extends BaseActiveRecord
{
    const TABLE_NAME    = "area_skill_link";

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
            [['area_id', 'skill_id'], 'integer', 'min' => 1],
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
