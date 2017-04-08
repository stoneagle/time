<?php

use yii\db\Schema;
use yii\db\Migration;

class m170320_133650_depend_skill_link_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%depend_skill_link}}', [
            'id'        => Schema::TYPE_PK,
            'skill_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "技能id"',
            'depend_id' => Schema::TYPE_STRING . ' NOT NULL DEFAULT 0 COMMENT "前置技能"',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%depend_skill_link}}');
        return false;
    }
}
