<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-17
 * Time: 1:30 PM
 */

namespace app\models;
use yii\base\Model;


class WpComConfigForm extends Model
{
    public $clientId;
    public $clientSecret;

    public function rules()
    {
        return [
            [['clientId', 'clientSecret'], 'required'],
        ];
    }

    public function getConfig()
    {
        return [
            'type' => 'com',
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
        ];
    }
}