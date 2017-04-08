<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_112939_assets_user_link_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%assets_user_link}}', [
            'id'         => Schema::TYPE_PK,
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "用户ID"',
            'assets_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属资产ID"',
            'entity_ids' => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "所属实体类别" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%assets_user_link}}');
    }
}
