<?php

namespace app\controllers;

use automattic\Rest\OrgWpApi;
use automattic\Rest\ComWpApi;
use Yii;
use yii\web\Controller;
use yii\helpers\FileHelper;

class BaseController extends Controller
{
	protected function getApi()
	{
		$files = FileHelper::findFiles(Yii::$app->runtimePath, [
			'recursive' => false,
			'only' => ['/api.cfg'],
		]);

		if (count($files) == 0)
			return null;

		$cfg = json_decode(file_get_contents($files[0]));
		switch($cfg->type)
		{
			case 'org':
				return new OrgWpApi($cfg);
			case 'com':
				if (!Yii::$app->user->isGuest)
					$cfg->token = Yii::$app->user->identity->token;
				return new ComWpApi($cfg);
			default:
				throw new \InvalidArgumentException('invalid API type: ' . $cfg['type']);
		}
	}

	protected function saveConfig($api)
	{
		$file = Yii::$app->runtimePath . '/api.cfg';
		file_put_contents($file, json_encode($api->getConfig()));
	}
}