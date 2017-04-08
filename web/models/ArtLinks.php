<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class ArtLinks extends BaseActiveRecord
{
    const TABLE_NAME    = "art_user_link";
    public $project_name;
    public $priority_id;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['priority_id'], 'integer'],
            [['work_ids', 'ctime', 'utime'], 'safe'],
            [['project_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'project_name' => '项目名称',
            'priority_id'  => '优先级',
            'work_ids'     => '相关作品列表',
            'ctime'        => '创建时间',
            'utime'        => '更新时间',
        ];
    }

    public function getQuery()
    {
        $art_t = self::tableName();
        $project_t   = Project::tableName();
        $query       = self::find()
            ->select("$art_t.*, $project_t.text project_name, $project_t.id project_id")
            ->leftJoin($project_t, "$project_t.obj_id = $art_t.id")
            ->andFilterWhere(["$project_t.field_id" => Config::FIELD_ART])
            ->andFilterWhere(["$project_t.text" => $this->project_name])
            ->andFilterWhere(["$art_t.user_id" => $this->user_id]);
        return $query;
    }
}
