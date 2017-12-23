<?php

use yii\db\Schema;
use yii\db\Migration;

class m171222_112535_daily_scheduler extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%daily_scheduler}}', [
            'id'         => Schema::TYPE_PK,
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始时间" ',
            'end_date'   => Schema::TYPE_DATETIME.' NOT NULL COMMENT "结束时间" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'daily_id'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属作息" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%daily_scheduler}}');
    }
}
