<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-17
 * Time: 11:14 AM
 */

namespace automattic\Yii;

use yii\base\Object;
use Yii;
use yii\helpers\FileHelper;
use automattic\LinkoScope\OrgLinkoScope;
use automattic\LinkoScope\ComLinkoScope;

/**
 * Class YiiWpApi
 * @package automattic\Yii
 */
class WpApi extends Object {
	public $configPath = '@runtime';
	public $configFileName = 'api.cfg';

	public function getRestApi()
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

		switch($cfg['type'])
		{
			case 'org':
				return new OrgLinkoScope($cfg);
			case 'com':
				return new ComLinkoScope($cfg);
			default:
				return null;
		}
	}

    protected function saveConfig($api)
    {
        $file = Yii::$app->runtimePath . '/api.cfg';
        file_put_contents($file, json_encode($api->getConfig()));
    }
}