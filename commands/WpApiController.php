<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use HttpRequest;

/**
 * This command tests interaction with the WordPress APIs
 *
 * @author Nabeel Sulieman <nabsul@outlook.com>
 * @since 2.0
 */
class WpApiController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    /**
     * This command tries to connect to the website via .org methods
     */
    public function actionOrg()
    {
        $url = 'http://localhost/auto';
        $req = new HttpRequest();
        $req->setUrl($url);
        $req->setMethod(HTTP_METH_HEAD);
        $req->send();

        $message = $req->getResponseMessage();

        print_r($message);
    }
}
