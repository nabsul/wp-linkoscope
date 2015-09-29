<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-28
 * Time: 1:16 PM
 */

namespace automattic\LinkoScope;

use automattic\Rest\ComWpApi;
use automattic\LinkoScope\Models\Link;
use automattic\LinkoScope\Models\Comment;
use yii\log\Logger;

class ComLinkoScope extends ComWpApi
{
    private $requestUrl = '/oauth2/token';
    private $authorizeUrl = '/oauth2/authorize';
    private $wpBase = 'https://public-api.wordpress.com';

    public function __construct(array $config)
    {
        $this->type = 'com';
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];
        $this->redirectUrl = $config['redirectUrl'];
        $this->blogId = isset($config['blogId']) ? $config['blogId'] : null;
        $this->blogUrl = isset($config['blogUrl']) ? $config['blogUrl'] : null;
        $this->token = isset($config['accessToken']) ? $config['accessToken'] : null;
        $this->baseUrl = $this->wpBase;
    }

    public function getConfig(){
        return [
            'type' => 'com',
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUrl' => $this->redirectUrl,
            'blogId' => $this->blogId,
            'blogUrl' => $this->blogUrl,
        ];
    }

    public function authorize(){
        return
            $this->baseUrl . $this->authorizeUrl .
            "?client_id=$this->clientId&redirect_uri=$this->redirectUrl&response_type=code" .
            ($this->blogId !== null ? "&blog=$this->blogId" : '');
    }

    public function token($code)
    {
        return $this->post($this->requestUrl, [], array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ));
    }

    public function getLinks(){
        $result = $this->get("/rest/v1.1/sites/$this->blogId/posts");
        if (!isset($result['posts']))
            return [];

        return ($this->convertPosts($result['posts']));
    }

    public function getLink($id){
        $result = $this->get("/rest/v1.1/sites/$this->blogId/posts/$id");
        return $this->convertPosts([$result])[0];
    }

    public function addLink(Link $link){
        return $this->post("/rest/v1.1/sites/$this->blogId/posts/new", [],
            [
                'title' => $link->title,
                'content' => $link->url,
            ]
        );
    }

    public function updateLink(Link $link){
        return $this->put("/rest/v1.1/sites/$this->blogId/posts/$link->id", [],
            [
                'title' => $link->title,
                'content' => $link->url,
            ]
        );
    }

    public function likeLink($id, $userId = null)
    {
        return $this->post("/rest/v1.1/sites/$this->blogId/posts/$id/likes/new");
    }

    public function unlikeLink($id, $userId = null)
    {
        return $this->post("/rest/v1.1/sites/$this->blogId/posts/$id/likes/mine/delete");
    }

    public function likeComment($id, $userId = null)
    {
        return $this->post("/rest/v1.1/sites/$this->blogId/comments/$id/likes/new");
    }

    public function unlikeComment($id, $userId = null)
    {
        return $this->post("/rest/v1.1/sites/$this->blogId/comments/$id/likes/mine/delete");
    }

    public function deleteLink($id){
        return $this->post("/rest/v1.1/sites/$this->blogId/posts/$id/delete");
    }

    public function getTypes(){}

    public function getAccount(){
        return $this->get('/rest/v1.1/me');
    }

    public function getComments($postId){
        $result = $this->get("/rest/v1.1/sites/$this->blogId/posts/$postId/replies/");
        if (!isset($result->comments))
            return [];
        return $this->convertComments($result->comments);
    }

    public function addComment(Comment $comment) {
        return $this->post("/rest/v1.1/sites/$this->blogId/posts/$comment->postId/replies/new",
            [
                'content' => $comment->content,
            ]
        );
    }

    public function deleteComment($id)
    {
        return $this->post("/rest/v1.1/sites/$this->blogId/comments/$id/delete");
    }

    private function convertPosts($posts)
    {
        \Yii::getLogger()->log('converting: ' . json_encode($posts), Logger::LEVEL_INFO);
        $result = [];
        foreach ($posts as $p)
        {
            $result[] = new Link([
                'id' => $p['ID'],
                'title' => $p['title'],
                'url' => $p['content'],
                'votes' => $p['like_count'],
            ]);
        }
        return $result;
    }

    private function convertComments($comments)
    {
        $result = [];
        foreach ($comments as $c)
        {
            $result[] = new Comment([
                'id' => $c->ID,
                'postId' => $c->post->ID,
                'content' => $c->content,
                'author' => $c->author->name,
                'votes' => $c->like_count,
            ]);
        }
        return $result;
    }
}