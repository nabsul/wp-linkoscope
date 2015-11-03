<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-18
 * Time: 8:37 AM
 */

namespace app\models;

use yii\base\Model;

class LinkForm extends Model
{
    public $title;
    public $url;
    public $tags = [];

    public function rules()
    {
        return [
            [['title', 'url'], 'required'],
            [['tags'], 'safe'],
            ['url', 'url'],
        ];

    }
}
