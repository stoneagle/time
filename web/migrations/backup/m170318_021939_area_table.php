<?php

use yii\db\Schema;
use yii\db\Migration;

class m170318_021939_area_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%area}}', [
            'id'       => Schema::TYPE_PK,
            'name'     => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'parent'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "父id"',
            'level'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属层级"',
            'field_id' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "所属领域id"',
            'del'      => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "软删除"',
            'ctime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%area}}');
        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
