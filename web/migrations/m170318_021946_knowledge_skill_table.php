<?php

use yii\db\Schema;
use yii\db\Migration;

class m170318_021946_knowledge_skill_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%knowledge_skill}}', [
            'id'          => Schema::TYPE_PK,
            'title'       => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "技能名称" ',
            'description' => Schema::TYPE_TEXT.' NOT NULL COMMENT "技能描述" ',
            'rank_desc'   => Schema::TYPE_TEXT.' COMMENT "等级描述，用逗号分隔" ',
            'img_url'     => Schema::TYPE_TEXT.' COMMENT "技能图片" ',
            'max_points'  => Schema::TYPE_SMALLINT.' NOT NULL COMMENT "最大级别" ',
            'type_id'     => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "技能类别"',
            'ctime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%knowledge_skill}}');
        return false;
    }
}
