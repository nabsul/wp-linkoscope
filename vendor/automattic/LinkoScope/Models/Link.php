<?php

namespace automattic\LinkoScope\Models;

use yii\base\Object;

class Link extends Object
{
	public $id;
	public $title;
	public $url;
	public $votes;
	public $score;
}