<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-18
 * Time: 8:37 AM
 */

namespace app\models;

use yii\base\Model;

class CommentForm extends Model
{
    public $comment;

    public function rules()
    {
        return [
            [['comment'], 'required'],
        ];

    }
}