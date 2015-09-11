<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use GuzzleHttp\Client;

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
        $client = new Client();
        $req = $client->request('HEAD', $url);

        $status = $req->getStatusCode();
        echo "HEAD request status: $status\n";
        if (200 != $status)
        {
            echo "Did not get a valid response for HEAD request";
            return;
        }

        $baseUrl = null;
        foreach ($req->getHeader('Link') as $h)
        {
            if (1 == preg_match('/^<(http.*)>.*https:\/\/github.com\/WP-API\/WP-API/', $h, $matches))
            {
                $baseUrl = $matches[1];
                echo "BaseUrl found: $baseUrl\n";
                break;
            }
        }

        if (null == $baseUrl)
        {
            echo "Base URL not found";
            return;
        }
    }
}
