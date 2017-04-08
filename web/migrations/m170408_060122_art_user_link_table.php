<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_060122_art_user_link_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%art_user_link}}', [
            'id'       => Schema::TYPE_PK,
            'user_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "用户ID"',
            'work_ids' => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "相关作品" ',
            'ctime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%art_user_link}}');
    }
}
