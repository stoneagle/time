<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\models\KnowledgeSkill;

class UserSkillLink extends BaseActiveRecord
{
    const TABLE_NAME    = "user_skill_link";

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
            [['level', 'skill_id'], 'integer', 'min' => 1],
            [['ctime', 'utime'], 'safe'],
            //[['name'], 'string', 'max' => 255],
        ];
    }

    public function getQuery()
    {
        $user_skill_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$user_skill_t.skill_id" => $this->skill_id]);
        $query->andFilterWhere(["$user_skill_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$user_skill_t.level" => $this->level]);
        return $query;
    }

    public function getSkillDict($multi_flag = false)
    {
        $query = $this->getQuery()->select("id, skill_id, level")->orderBy("skill_id, level");
        $user_skill_list = $query->asArray()->all();
        $user_skill_arr = ArrayHelper::getColumn($user_skill_list, "skill_id");
        if (!$multi_flag) {
            $skill_arr = KnowledgeSkill::find()
                ->select("id, title")
                ->andWhere(["id" => array_unique($user_skill_arr)]) 
                ->asArray()->all();
            $skill_dict = ArrayHelper::map($skill_arr, "id", "title");
            $ret = [];
            foreach ($user_skill_list as $one) {
                $skill_name = ArrayHelper::getValue($skill_dict, $one["skill_id"]);
                $ret[$one["id"]] = $skill_name."[lv".$one["level"]."]";
            }
        } else {
            $skill_t     = KnowledgeSkill::tableName();
            $link_t      = AreaSkillLink::tableName();
            $area_t      = KnowledgeArea::tableName();
            $skill_arr = KnowledgeSkill::find()
                ->select("$skill_t.id, $skill_t.title, $area_t.name as area_name")
                ->leftJoin($link_t, "$skill_t.id = $link_t.skill_id")
                ->leftJoin($area_t, "$link_t.area_id = $area_t.id")
                ->andWhere(["$skill_t.id" => array_unique($user_skill_arr)]) 
                ->asArray()->all();
            $skill_dict = ArrayHelper::index($skill_arr, "id");
            $ret = [];
            foreach ($user_skill_list as $one) {
                $skill_name = ArrayHelper::getValue($skill_dict, $one["skill_id"])["title"];
                $area_name = ArrayHelper::getValue($skill_dict, $one["skill_id"])["area_name"];
                $ret[$area_name][$one["id"]] = $skill_name."[lv".$one["level"]."]";
            } 
        }
        return $ret;
    }
}
