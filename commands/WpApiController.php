<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\ClientException;

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
ID: 24
Key: H0LzFuk95DvY
Secret: cnTCuCoiZyC9a2eZa3RHJrP0w550b1eDgruGLYnPcQXKNFyK
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

        echo "\ntrying basic auth\n";
        $baseUrl = $baseUrl . "wp/v2/";
        $client = new Client(['base_uri' => $baseUrl]);
        $req = $client->request('GET', 'users', ['auth'=>['nabeel', 'nabeel']]);
        echo $req->getBody()->getContents();

        echo "\ntrying oauth\n";
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key'    => 'H0LzFuk95DvY',
            'consumer_secret' => 'cnTCuCoiZyC9a2eZa3RHJrP0w550b1eDgruGLYnPcQXKNFyK',
            'token'           => null,//'my_token',
            'token_secret'    => null,//'my_token_secret'
            'request_method'  => Oauth1::REQUEST_METHOD_QUERY,
        ]);
        $stack->push($middleware);
        $client = new Client([
            'base_uri' => 'http://localhost/auto/',
            'handler' => $stack
        ]);

        // Set the "auth" request option to "oauth" to sign using oauth
        try
        {
            $res = $client->get('oauth1/request', ['auth' => 'oauth']);
            $body = $res->getBody()->getContents();
        }
        catch (ClientException $ex)
        {
            echo $ex->getMessage();
            echo "\n";
            return;
        }

        parse_str($body, $pars);
        $token = $pars['oauth_token'];
        $secret = $pars['oauth_token_secret'];
        echo "\nOauth request worked.\ntoken: $token\nsecret: $secret";
    }
}
