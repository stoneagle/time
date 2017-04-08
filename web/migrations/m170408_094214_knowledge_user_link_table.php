<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_094214_knowledge_user_link_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%knowledge_user_link}}', [
            'id'         => Schema::TYPE_PK,
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "用户ID"',
            'entity_ids' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "所属实体类别" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%knowledge_user_link}}');
    }
}
