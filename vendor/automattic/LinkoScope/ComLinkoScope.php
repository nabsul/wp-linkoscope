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

class ComLinkoScope
{
    public $type = 'com';
    private $api;
    private $likeFactor = 24*60*60;

    public function __construct(ComWpApi $api){
        $this->api = $api;
    }

    public function getConfig(){
        return $this->api->getConfig();
    }

    public function authorize(){
        return $this->api->getAuthorizeUrl();
    }

    public function token($code)
    {
        return $this->api->getToken($code);
    }

    public function getLinks(){
        $result = $this->api->listPosts();
        if (!isset($result['posts']))
            return [];

        return ($this->convertPosts($result['posts']));
    }

    public function getLink($id){
        $result = $this->api->getPost($id);
        return $this->convertPosts([$result])[0];
    }

    public function addLink(Link $link){
        return $this->api->addPost([
            'title' => $link->title,
            'content' => $link->url,
            'status' => 'publish',
        ]);
    }

    public function updateLink(Link $link){
        return $this->api->updatePost($link->id, [
            'title' => $link->title,
            'content' => $link->url,
            'date' => $link->date,
            'status' => 'publish',
        ]);
    }

    public function likeLink($id, $userId = null)
    {
        $link = $this->getLink($id);
        $link->date = date(DATE_ATOM, strtotime($link->date) + $this->likeFactor);
        $this->updateLink($link);
        return $this->api->likePost($id);
    }

    public function unlikeLink($id, $userId = null)
    {
        $link = $this->getLink($id);
        $link->date = date(DATE_ATOM, strtotime($link->date) - $this->likeFactor);
        $this->updateLink($link);
        return $this->api->unlikePost($id);
    }

    public function likeComment($id, $userId = null)
    {
        return $this->api->likeComment($id);
    }

    public function unlikeComment($id, $userId = null)
    {
        return $this->api->unlikeComment($id);
    }

    public function deleteLink($id){
        return $this->api->deletePost($id);
    }

    public function getTypes(){return [];}

    public function getAccount(){
        return $this->api->getSelf();
    }

    public function getComments($postId){
        $result = $this->api->listComments($postId);
        if (!isset($result->comments))
            return [];
        return $this->convertComments($result->comments);
    }

    public function addComment(Comment $comment) {
        return $this->api->addComment([
            'content' => $comment->content,
        ]);
    }

    public function deleteComment($id)
    {
        return $this->api->deleteComment($id);
    }

    private function convertPosts($posts)
    {
        $result = [];
        foreach ($posts as $p)
        {
            $result[] = new Link([
                'id' => $p['ID'],
                'date' => $p['date'],
                'title' => $p['title'],
                'url' => $p['content'],
                'votes' => $p['like_count'],
                'score' => strtotime($p['date']),
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