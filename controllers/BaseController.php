<?php

namespace app\controllers;

use ShortCirquit\LinkoScopeApi\ComLinkoScope;
use ShortCirquit\LinkoScopeApi\OrgLinkoScope;
use Yii;
use yii\web\Controller;
use yii\helpers\FileHelper;

class BaseController extends Controller
{
    /**
     * @return ComLinkoScope|OrgLinkoScope|null
     */
    protected function getApi()
	{
		$files = FileHelper::findFiles(Yii::$app->runtimePath, [
			'recursive' => false,
			'only' => ['/api.cfg'],
		]);

		if (count($files) == 0)
			return null;

		$cfg = json_decode(file_get_contents($files[0]), true);
		if (!Yii::$app->user->isGuest)
		{
			$cfg['accessToken'] = Yii::$app->user->identity->token;
			$cfg['accessTokenSecret'] = Yii::$app->user->identity->secret;
		}

		$classNAme = $cfg['type'];
        $api = new $classNAme($cfg);
        return $api;
	}

	protected function saveConfig($api)
	{
		$file = Yii::$app->runtimePath . '/api.cfg';
		$cfg = $api->getConfig();
        $cfg['type'] = get_class($api);
		file_put_contents($file, json_encode($cfg, JSON_PRETTY_PRINT));
	}
}
