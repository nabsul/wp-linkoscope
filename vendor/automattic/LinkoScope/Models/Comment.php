<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-21
 * Time: 8:45 AM
 */

namespace automattic\LinkoScope\Models;


use yii\base\Object;

class Comment extends Object
{
    public $id;
    public $date;
    public $postId;
    public $author;
    public $content;
    public $score;
    public $votes;
    public $likeList;
}