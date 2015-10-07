<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $link_id
 * @property integer $date
 * @property integer $user_id
 * @property string $username
 * @property string $comment
 * @property integer $vote_count
 * @property integer $score
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'link_id', 'date', 'user_id', 'vote_count', 'score'], 'integer'],
            [['comment'], 'string'],
            [['username'], 'string', 'max' => 255],
            [['comment_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_id' => 'Comment ID',
            'link_id' => 'Link ID',
            'date' => 'Date',
            'user_id' => 'User ID',
            'username' => 'Username',
            'comment' => 'Comment',
            'vote_count' => 'Vote Count',
            'score' => 'Score',
        ];
    }
}
