<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-21
 * Time: 8:45 AM
 */

namespace automattic\Rest\Models;


use yii\base\Object;

class Comment extends Object
{
    public $id;
    public $postId;
    public $content;
    public $votes;
}