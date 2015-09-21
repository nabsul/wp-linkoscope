<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest\Org;

use automattic\Rest\JsonOauth1;
use automattic\Rest\iWpApi;
use yii\authclient\OAuthToken;
use automattic\Rest\Models\Link;
use yii\base\Object;
use automattic\Rest\Models\Comment;
use Yii;

class OrgWpApi extends Object implements  iWpApi
{
    public $type;
    public $consumerKey = 'H0LzFuk95DvY';
    public $consumerSecret = 'cnTCuCoiZyC9a2eZa3RHJrP0w550b1eDgruGLYnPcQXKNFyK';
    public $blogUrl = 'http://localhost/auto';
    public $token = null;

    private $requestUrl =   '/oauth1/request';
    private $authorizeUrl = '/oauth1/authorize';
    private $accessUrl =    '/oauth1/access';
    private $postUrl =      '/wp-json/wp/v2/linkolink';
    private $typeUrl =      '/wp-json/wp/v2/types';
    private $selfUrl =      '/wp-json/wp/v2/users/me';
    private $commentsUrl =  '/wp-json/wp/v2/comments';


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
        $links = $this->get($this->postUrl);
        $convert = function($item){
            return new Link([
                'id' => $item['id'],
                'title' => $item['title']['rendered'],
                'url' => $item['excerpt']['rendered'],
                'votes' => $item['menu_order'],
            ]);
        };

        return array_map($convert, $links);
    }

    public function getLink($id)
    {
        $link = $this->get($this->postUrl . "/$id");
        return new Link([
                'id' => $link['id'],
                'title' => $link['title']['rendered'],
                'url' => $link['excerpt']['rendered'],
                'votes' => $link['menu_order'],
            ]);
    }

    public function addLink(Link $link)
    {
        $body = [
            'title' => $link->title,
            'excerpt' => $link->url,
            'menu_order' => 0,
            'status' => 'publish',
        ];
        return $this->post($this->postUrl, $body);
    }

    public function updateLink(Link $link)
    {
        $body = [
            'title' => $link->title,
            'excerpt' => $link->url,
            'menu_order' => $link->votes,
            'status' => 'publish',
        ];

        return $this->put($this->postUrl . "/{$link->id}", $body);
    }

    public function deleteLink($id)
    {
        return $this->delete($this->postUrl . "/$id");
    }

    public function getTypes()
    {
        return $this->get($this->typeUrl);
    }

    public function getAccount()
    {
        return $this->get($this->selfUrl);
    }

    public function getComments($postId)
    {
        return $this->get($this->commentsUrl, ['post' => $postId]);
    }

    public function addComment(Comment $comment)
    {
        $body = [
            'post' => $comment->postId,
            'content' => $comment->content,
            'author' => Yii::$app->user->id,
            'author_name' => Yii::$app->user->getIdentity()->username,
        ];

        return $this->post($this->commentsUrl, $body);
    }

    private function get($url, $params = [])
    {
        return $this->send('GET', $url, $params);
    }

    private function post($url, $body)
    {
        return $this->send('POST', $url, ['body' => $body]);
    }

    private function delete($url)
    {
        return $this->send('DELETE', $url);
    }

    private function put($url, $body)
    {
        return $this->send('PUT', $url, ['body' => $body]);
    }

    private function send($method, $url, $params = [], $headers = [])
    {
        $url = $this->blogUrl . $url;
        return $this->getAuthClient()->api($url, $method, $params, $headers);
    }

    private function getAuthClient()
    {
        $client = new JsonOauth1 ([
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'authUrl' => $this->blogUrl . $this->authorizeUrl,
            'requestTokenUrl' => $this->blogUrl . $this->requestUrl,
            'accessTokenUrl' => $this->blogUrl . $this->accessUrl,
            'curlOptions' => [
                CURLOPT_FOLLOWLOCATION => true,
            ],
        ]);
        if ($this->token != null)
            $client->accessToken = $this->token;
        return $client;
    }
}