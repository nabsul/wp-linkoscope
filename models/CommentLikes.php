<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-10-05
 * Time: 4:25 PM
 */

namespace app\models;


use yii\db\ActiveRecord;

class CommentLikes extends ActiveRecord
{
    public $id;
    public $comment_id;
    public $user_id;
}