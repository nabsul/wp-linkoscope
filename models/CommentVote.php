<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment_vote".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $user_id
 */
class CommentVote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment_vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id'], 'integer'],
            [['comment_id', 'user_id'], 'unique', 'targetAttribute' => ['comment_id', 'user_id'], 'message' => 'The combination of Comment ID and User ID has already been taken.']
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
            'user_id' => 'User ID',
        ];
    }
}
