<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use app\models\ArtEntity;
use yii\helpers\ArrayHelper;

class ArtWork extends BaseActiveRecord
{
    const TABLE_NAME    = "art_work";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['country_id', 'entity_id', 'year'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => '实体名称',
            'country_id' => '所属国家',
            'entity_id'  => '所属实体',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $work_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$work_t.year"       => $this->year]);
        $query->andFilterWhere(["$work_t.name"       => $this->name]);
        $query->andFilterWhere(["$work_t.entity_id"  => $this->entity_id]);
        $query->andFilterWhere(["$work_t.country_id" => $this->country_id]);
        return $query;
    }

    public function getListDict()
    {
        $work_arr = self::find()
            ->select("id, name")
            ->asArray()->all();
        return ArrayHelper::map($work_arr, "id", "name");
    }

    public function getMultiSelectDict()
    {
        $entity_dict = ArtEntity::getChildDict(); 
        $work_arr = self::find()
            ->select("id, name, entity_id")
            ->asArray()->all();
        $work_dict = [];
        foreach ($work_arr as $one) {
            $entity_name = ArrayHelper::getValue($entity_dict, $one["entity_id"]);
            $work_dict[$entity_name][$one["id"]] = $one["name"];
        }
        return $work_dict;
    }
}
