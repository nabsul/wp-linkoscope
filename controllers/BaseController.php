<?php

namespace app\controllers;

use ShortCirquit\LinkoScopeApi\iLinkoScope;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * @return iLinkoScope
     */
    protected function getApi()
	{
		return Yii::$app->linko->getApi();
	}

	protected function saveConfig($api)
	{
		Yii::$app->linko->saveConfig($api);
	}
}
