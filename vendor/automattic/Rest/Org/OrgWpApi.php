<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest\Org;

use automattic\Rest\iWpApi;
use yii\authclient\OAuth1;
use yii\authclient\OAuthToken;
use automattic\Rest\Models\Link;
use yii\base\Object;

class OrgWpApi extends Object implements  iWpApi
{
    public $consumerKey = 'H0LzFuk95DvY';
    public $consumerSecret = 'cnTCuCoiZyC9a2eZa3RHJrP0w550b1eDgruGLYnPcQXKNFyK';
    public $blogUrl = 'http://localhost/auto';
    public $token = null;

    private $requestUrl = '/oauth1/request';
    private $authorizeUrl = '/oauth1/authorize';
    private $accessUrl = '/oauth1/access';
    private $postUrl = '/wp-json/wp/v2/linkolink';
    private $typeUrl = '/wp-json/wp/v2/types';


    public function getConfig()
    {
        return [
            'type' => 'org',
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'blogUrl' => $this->blogUrl,
        ];
    }

    /**
     * Gets an initial authorization token and returns the URL to go to get user consent
     *
     * @param $returnUrl string URL to return from after user authorization.
     * @return string
     */
    public function authorize($returnUrl)
    {
        $oauthClient = $this->getAuthClient();
        $requestToken = $oauthClient->fetchRequestToken(); // Get request token
        $url = $oauthClient->buildAuthUrl($requestToken, ['oauth_callback' => $returnUrl]); // Get authorization URL
        return $url; // Redirect to authorization URL
    }

    /**
     * @param $token
     * @param $verifier
     * @return OAuthToken
     * @throws \yii\base\Exception
     */
    public function access($token, $verifier)
    {
        $client = $this->getAuthClient();
        return $client->fetchAccessToken(new OAuthToken(['token' => $token]), $verifier);
    }

    public function getLinks()
    {
        $links = $this->get($this->postUrl, $this->token);
        $convert = function($item){
            return new Link([
                'id' => $item['id'],
                'title' => $item['title']['rendered'],
                'url' => $item['excerpt']['rendered'],
                'summary' => $item['content']['rendered'],
                'votes' => $item['menu_order'],
            ]);
        };

        return array_map($convert, $links);
    }

    public function getTypes()
    {
        return $this->get($this->typeUrl);
    }

    private function get($url)
    {
        $url = $this->blogUrl . $url;
        return $this->getAuthClient()->api($url, 'GET', []);
    }

    private function getAuthClient()
    {
        $client = new OAuth1([
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'authUrl' => $this->blogUrl . $this->authorizeUrl,
            'requestTokenUrl' => $this->blogUrl . $this->requestUrl,
            'accessTokenUrl' => $this->blogUrl . $this->accessUrl,
        ]);
        if ($this->token != null)
            $client->accessToken = $this->token;
        return $client;
    }
}