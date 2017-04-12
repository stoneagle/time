<?php

use yii\db\Schema;
use yii\db\Migration;

class m170410_061348_target_entity_link_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%target_entity_link}}', [
            'id'        => Schema::TYPE_PK,
            'target_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "目标ID"',
            'entity_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "实体ID"',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%target_entity_link}}');
    }
}
