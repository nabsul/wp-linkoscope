<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-22
 * Time: 9:00 AM
 */

namespace automattic\Rest;

use yii\helpers\FileHelper;

class WpApi
{
    public static function getApi()
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
                return new ComWpApi($cfg);
            default:
                throw new \InvalidArgumentException('invalid API type: ' . $cfg['type']);
        }
    }

    public static function saveConfig(iWpApi $api)
    {
        $file = Yii::$app->runtimePath . '/api.cfg';
        file_put_contents($file, json_encode($api->getConfig()));
    }

    public static function login()
    {
    }
}