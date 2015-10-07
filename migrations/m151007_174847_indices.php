<?php

use yii\db\Schema;
use yii\db\Migration;

class m151007_174847_indices extends Migration
{
    public function up()
    {
        $this->createIndex('idx_link_post_id', 'link', 'post_id', true);
        $this->createIndex('idx_link_score', 'link', 'score', false);
        $this->createIndex('idx_link_date', 'link', 'date', false);

        $this->createIndex('idx_link_vote_link_id', 'link_vote', 'link_id', false);
        $this->createIndex('idx_link_vote_user_id', 'link_vote', ['link_id', 'user_id'], true);

        $this->createIndex('idx_comment_comment_id', 'comment', 'comment_id', true);
        $this->createIndex('idx_comment_link_id', 'comment', 'link_id', false);
        $this->createIndex('idx_comment_score', 'comment', 'score', false);
        $this->createIndex('idx_comment_date', 'comment', 'date', false);

        $this->createIndex('idx_comment_vote_link_id', 'comment_vote', 'comment_id', false);
        $this->createIndex('idx_comment_vote_user_id', 'comment_vote', ['comment_id', 'user_id'], true);

    }

    public function down()
    {
        $this->dropIndex('idx_link_post_id', 'link');
        $this->dropIndex('idx_link_score', 'link');
        $this->dropIndex('idx_link_date', 'link');

        $this->dropIndex('idx_link_vote_link_id', 'link_vote');
        $this->dropIndex('idx_link_vote_user_id', 'link_vote');

        $this->dropIndex('idx_comment_comment_id', 'comment');
        $this->dropIndex('idx_comment_link_id', 'comment');
        $this->dropIndex('idx_comment_score', 'comment');
        $this->dropIndex('idx_comment_date', 'comment');

        $this->dropIndex('idx_comment_vote_link_id', 'link_vote');
        $this->dropIndex('idx_comment_vote_user_id', 'link_vote');
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
