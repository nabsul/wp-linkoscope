<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-17
 * Time: 1:30 PM
 */

namespace app\models;

use yii\base\Model;


class WpOrgConfigForm extends Model
{
    public $blogUrl;
    public $consumerKey;
    public $consumerSecret;

    public function rules()
    {
        return [
            [['blogUrl', 'consumerKey', 'consumerSecret'], 'required'],
        ];
    }
}
