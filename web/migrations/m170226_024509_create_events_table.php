<?php

use yii\db\Migration;

/**
 * Handles the creation of table `events`.
 */
class m170226_024509_create_events_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('events', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('events');
    }
}
