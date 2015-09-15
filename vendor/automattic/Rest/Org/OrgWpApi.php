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

class OrgWpApi implements  iWpApi
{
    public $consumerKey = 'H0LzFuk95DvY';
    public $consumerSecret = 'cnTCuCoiZyC9a2eZa3RHJrP0w550b1eDgruGLYnPcQXKNFyK';
    public $blogUrl = 'http://localhost/auto';
    public $requestUrl = 'http://localhost/auto/oauth1/request';
    public $authorizeUrl = 'http://localhost/auto/oauth1/authorize';
    public $accessUrl = 'http://localhost/auto/oauth1/access';
    public $postUrl = 'http://localhost/auto/wp-json/wp/v2/posts';
    public $typeUrl = 'http://localhost/auto/wp-json/wp/v2/types';

    public $token = null;
    public $secret = null;
    public $verifier = null;

    /**
     * @var Client
     */
    private $_client = null;

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

    public function getLinks($tok)
    {
        return $this->get($this->postUrl, $tok);
    }

    public function getTypes($tok)
    {
        return $this->get($this->typeUrl, $tok);
    }

    private function get($url, $tok)
    {
        $oauthClient = $this->getAuthClient();
        $oauthClient->accessToken = $tok;
        $result = $oauthClient->api($url, 'GET', []);
        return $result;
    }

    private function getAuthClient()
    {
        return new OAuth1([
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'authUrl' => $this->authorizeUrl,
            'requestTokenUrl' => $this->requestUrl,
            'accessTokenUrl' => $this->accessUrl,
        ]);

    }
}