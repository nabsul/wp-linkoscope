<?php

use yii\db\Schema;
use yii\db\Migration;

class m151007_141657_initial_db extends Migration
{
    public function up()
    {
        $this->createTable('link',[
            'id',
            'post_id',
            'date',
            'user_id',
            'username',
            'title',
            'url',
            'vote_count',
            'score',
        ]);

        $this->createTable('link_vote',[
            'id',
            'link_id',
            'user_id',
        ]);

        $this->createTable('comment',[
            'id',
            'link_id',
            'date',
            'user_id',
            'username',
            'comment',
            'vote_count',
            'score',
        ]);

        $this->createTable('comment_vote',[
            'id',
            'comment_id',
            'user_id',
        ]);
    }

    public function down()
    {
        echo "m151007_141657_initial_db cannot be reverted.\n";
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
