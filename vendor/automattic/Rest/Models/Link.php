<?php

namespace automattic\Rest\Models;

use yii\base\Object;

class Link extends Object
{
	public $id;
	public $title;
	public $url;
	public $summary;
	public $votes;
}