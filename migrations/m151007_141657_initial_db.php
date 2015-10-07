<?php

use yii\db\Schema;
use yii\db\Migration;

class m151007_141657_initial_db extends Migration
{
    public function up()
    {
        $this->createTable('link',[
            'id' => 'pk',
            'post_id' => 'integer',
            'date' => 'datetime',
            'user_id' => 'integer',
            'username' => 'string',
            'title' => 'text',
            'url' => 'string',
            'vote_count' => 'integer',
            'score' => 'integer',
        ]);

        $this->createTable('link_vote',[
            'id' => 'pk',
            'link_id' => 'integer',
            'user_id' => 'integer',
        ]);

        $this->createTable('comment',[
            'id' => 'pk',
            'comment_id' => 'integer',
            'link_id' => 'integer',
            'date' => 'integer',
            'user_id' => 'integer',
            'username' => 'string',
            'comment' => 'text',
            'vote_count' => 'integer',
            'score' => 'integer',
        ]);

        $this->createTable('comment_vote',[
            'id' => 'pk',
            'comment_id' => 'integer',
            'user_id' => 'integer',
        ]);

    }

    public function down()
    {
        $this->dropTable('link');
        $this->dropTable('link_vote');
        $this->dropTable('comment');
        $this->dropTable('comment_vote');
    }
}
