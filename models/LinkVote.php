<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "link_vote".
 *
 * @property integer $id
 * @property integer $link_id
 * @property integer $user_id
 */
class LinkVote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'link_vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['link_id', 'user_id'], 'integer'],
            [['link_id', 'user_id'], 'unique', 'targetAttribute' => ['link_id', 'user_id'], 'message' => 'The combination of Link ID and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'link_id' => 'Link ID',
            'user_id' => 'User ID',
        ];
    }
}
