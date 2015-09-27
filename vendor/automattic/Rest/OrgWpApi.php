<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest;

use yii\authclient\OAuthToken;
use automattic\LinkoScope\Models\Link;
use yii\base\Object;
use automattic\LinkoScope\Models\Comment;
use Yii;
use yii\log\Logger;

class OrgWpApi extends Object implements  iWpApi
{
    public $type;
    public $consumerKey;
    public $consumerSecret;
    public $blogUrl;
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
        $requestToken = new OAuthToken(['token' => $token]);
        $defaultParams = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_token' => $requestToken->getToken(),
            'oauth_verifier' => $verifier,
        ];

        $api = $this->getAuthClient();
        $response = $api->sendSignedRequest('GET', $this->blogUrl . $this->accessUrl, $defaultParams);
        Yii::getLogger()->log("params: " . json_encode($response), Logger::LEVEL_INFO);

        return $response;
    }

    public function getLinks()
    {
        $links = $this->get($this->postUrl,[
            'filter' => [
                'meta_key' => 'linkoscope_score',
                'order' => 'DESC',
                'orderby' => 'meta_value_num',
            ],
        ]);
        return array_map(function($i){return $this->convertLink($i);}, $links);
    }

    public function getLink($id)
    {
        $link = $this->get($this->postUrl . "/$id");
        return $this->convertLink($link);
    }

    private function convertLink($item)
    {
        return new Link([
            'id' => $item['id'],
            'title' => $item['title']['raw'],
            'url' => $item['content']['raw'],
            'votes' => count($item['linkoscope_likes']) > 0 ? count(explode(';', $item['linkoscope_likes'][0])) : 0,
            'score' => count($item['linkoscope_score']) > 0 ? $item['linkoscope_score'][0] : 0,
        ]);
    }

    public function addLink(Link $link)
    {
        $body = [
            'title' => $link->title,
            'content' => $link->url,
            'status' => 'publish',
            'linkoscope_score' => time(),
        ];
        return $this->post($this->postUrl, $body);
    }

    public function updateLink(Link $link)
    {
        $body = [
            'title' => $link->title,
            'content' => $link->url,
        ];

        return $this->put($this->postUrl . "/{$link->id}", $body);
    }

    public function likeLink($id, $userId)
    {
        $key = 'linkoscope_likes';
        $url = $this->postUrl . "/$id";
        $link = $this->get($url);
        $likes = count($link[$key]) == 0 ? [] : explode(';', $link[$key][0]);
        $likes[] = $userId;

        //TODO: Uncomment this when testing is complete
        //$likes = array_unique($likes);

        return $this->put($url, [
            'linkoscope_likes' => implode(';', $likes),
            'linkoscope_score' => strtotime($link['date']) + 24 * 60 * 60 * count($likes),
        ]);
    }

    public function unlikeLink($id)
    {
        $link = $this->getLink($id);
        $link->votes--;
        return $this->updateLink($link);
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
        return $this->get($this->selfUrl, ['_envelope' => 1]);
    }

    public function getComments($postId)
    {
        $results = $this->get($this->commentsUrl, ['post' => $postId]);
        $ret = [];
        foreach ($results as $c)
        {
            $ret[] = new Comment([
                'id' => $c['id'],
                'postId' => $c['post'],
                'content' => $c['content']['rendered'],
                'votes' => 0,
                'author' => $c['author_name'],
            ]);
        }
        return $ret;
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

    public function likeComment($id)
    {

    }

    public function unlikeComment($id)
    {

    }

    public function deleteComment($id)
    {
        return $this->delete($this->commentsUrl . "/$id");
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
            $client->accessToken = ['params' => $this->token];
        return $client;
    }
}