<?php

use yii\db\Schema;
use yii\db\Migration;

class m170320_133630_talent_skill_link_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%talent_skill_link}}', [
            'id'        => Schema::TYPE_PK,
            'skill_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所处层级"',
            'talent_id' => Schema::TYPE_STRING . ' NOT NULL DEFAULT 0 COMMENT "相关天赋，用逗号分隔"',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%talent_skill_link}}');
        return false;
    }
}
