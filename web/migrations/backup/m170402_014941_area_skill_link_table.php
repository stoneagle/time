<?php

use yii\db\Schema;
use yii\db\Migration;

class m170402_014941_area_skill_link_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%area_skill_link}}', [
            'id'       => Schema::TYPE_PK,
            'skill_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "技能id"',
            'area_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "领域id"',
            'ctime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%area_skill_link}}');
        return false;
    }

}
