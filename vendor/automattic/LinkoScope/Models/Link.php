<?php

namespace automattic\LinkoScope\Models;

use yii\base\Object;

class Link extends Object
{
	public $id;
    public $date;
	public $authorId;
	public $title;
	public $url;
	public $votes;
    public $voteList;
	public $score;
}