<?php

namespace app\models;

use yii\base\Model;


class TagForm extends model
{
    public $name;

    public function rules(){
        return [
            [['name',], 'required'],
        ];
    }
}
