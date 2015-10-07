<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "link".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $date
 * @property integer $user_id
 * @property string $username
 * @property string $title
 * @property string $url
 * @property integer $vote_count
 * @property integer $score
 */
class Link extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'user_id', 'vote_count', 'score'], 'integer'],
            [['date'], 'safe'],
            [['title'], 'string'],
            [['username', 'url'], 'string', 'max' => 255],
            [['post_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'date' => 'Date',
            'user_id' => 'User ID',
            'username' => 'Username',
            'title' => 'Title',
            'url' => 'Url',
            'vote_count' => 'Vote Count',
            'score' => 'Score',
        ];
    }
}
