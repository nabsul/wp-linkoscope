<?php

use yii\db\Schema;
use yii\db\Migration;

class m151016_000058_create_jobs_table extends Migration
{
    public function up()
    {
        $this->createTable('jobs',[
            'id' => Schema::TYPE_PK,
            'date' => Schema::TYPE_TIMESTAMP,
            'started' => Schema::TYPE_DATETIME,
            'completed' => Schema::TYPE_DATETIME,
            'status' => Schema::TYPE_STRING,
            'type' => Schema::TYPE_STRING,
            'arguments' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('jobs');
    }
}
