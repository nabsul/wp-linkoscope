<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-28
 * Time: 1:13 PM
 */

namespace automattic\LinkoScope;


use automattic\Rest\OrgWpApi;
use automattic\LinkoScope\Models\Link;
use automattic\LinkoScope\Models\Comment;
use yii\log\Logger;

class OrgLinkoScope extends OrgWpApi
{
    private $requestUrl =   '/oauth1/request';
    private $authorizeUrl = '/oauth1/authorize';
    private $accessUrl =    '/oauth1/access';
    private $postUrl =      '/wp-json/wp/v2/linkolink';
    private $typeUrl =      '/wp-json/wp/v2/types';
    private $selfUrl =      '/wp-json/wp/v2/users/me';
    private $commentsUrl =  '/wp-json/wp/v2/comments';

    public function __construct(array $config)
    {
        $this->type = $config['type'];
        $this->baseUrl = $config['blogUrl'];
        $this->consumerKey = $config['consumerKey'];
        $this->consumerSecret = $config['consumerSecret'];
        $this->accessToken = isset($config['accessToken']) ? $config['accessToken'] : null;
        $this->accessTokenSecret = isset($config['accessTokenSecret']) ? $config['accessTokenSecret'] : null;
    }

    public function getConfig()
    {
        return [
            'type' => 'org',
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'blogUrl' => $this->baseUrl,
        ];
    }

    public function authorize($returnUrl)
    {
        $response = $this->get($this->requestUrl, ['oauth_callback' => $returnUrl]);
        \Yii::getLogger()->log(json_encode($response), Logger::LEVEL_INFO);
        $params = ['oauth_callback' => $returnUrl, 'oauth_token' => $response['oauth_token'],];
        return $this->baseUrl . $this->authorizeUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public function access($token, $verifier)
    {
        $defaultParams = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_token' => $token,
            'oauth_verifier' => $verifier,
        ];
        return $this->get($this->accessUrl, $defaultParams);
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
        return array_map([$this, 'convertLink'], $links);
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

        return $this->put($this->postUrl . "/{$link->id}", [], $body);
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
                'content' => $c['content']['raw'],
                'score' => $c['linkoscope_score'],
                'likes' => $c['karma'],
                'author' => $c['author_name'],
            ]);
        }
        return $ret;
    }

    public function getComment($id)
    {
        $c = $this->get($this->commentsUrl . "/$id");
        return new Comment([
            'id' => $c['id'],
            'postId' => $c['post'],
            'content' => $c['content']['raw'],
            'score' => $c['linkoscope_score'],
            'likes' => $c['karma'],
            'author' => $c['author_name'],
        ]);
    }

    public function addComment(Comment $comment)
    {
        $body = [
            'post' => $comment->postId,
            'content' => $comment->content,
        ];

        return $this->post($this->commentsUrl, [], $body);
    }

    public function updateComment(Comment $comment)
    {
        $body = [
            'post' => $comment->postId,
            'content' => $comment->content,
            'author_name' => $comment->author,
            'karma' =>  $comment->likes,
            'linkoscope_score' => $comment->score,
        ];

        return $this->put($this->commentsUrl . "/$comment->id", [], $body);
    }

    public function likeComment($id)
    {
        $comment = $this->getComment($id);
        $comment->likes++;
        $comment->score = $comment->likes * 100;
        return $this->updateComment($comment);
    }

    public function unlikeComment($id)
    {
        $comment = $this->getComment($id);
        $comment->likes--;
        $comment->score = $comment->likes * 100;
        return $this->updateComment($comment);
    }

    public function deleteComment($id)
    {
        return $this->delete($this->commentsUrl . "/$id");
    }
}