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

class ComLinkoScope
{
    public $type = 'com';
    private $api;
    private $likeFactor = 24*60*60;
    private $dateOffset = 60 * 60 * 24 * 365 * 100;

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

        return ($this->apiToLinks($result['posts']));
    }

    public function getLink($id){
        $result = $this->api->getPost($id);
        return $this->apiToLink($result);
    }

    public function addLink(Link $link){
        return $this->api->addPost($this->linkToApi($link));
    }

    public function updateLink(Link $link){
        return $this->api->updatePost($link->id, $this->linkToApi($link));
    }

    public function likeLink($id, $userId = null)
    {
        $this->api->likePost($id);
        $link = $this->getLink($id);
        return $this->updateLink($link);
    }

    public function unlikeLink($id, $userId = null)
    {
        $this->api->unlikePost($id);
        $link = $this->getLink($id);
        return $this->updateLink($link);
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

    private function apiToLinks($posts)
    {
        $result = [];
        foreach ($posts as $p)
        {
            $result[] = $this->apiToLink($p);
        }
        return $result;
    }

    private function apiToLink($p){
        return new Link([
            'id' => $p['ID'],
            'date' => $this->getMetaKeyValue($p, 'linkoscope_created'),
            'title' => $p['title'],
            'url' => $p['content'],
            'votes' => $p['like_count'],
            'score' => strtotime($p['date']),
        ]);
    }

    private function linkToApi(Link $link){
        $val = [
            'title' => $link->title,
            'content' => $link->url,
            'status' => 'publish',
        ];

        // If there is no created metadata, add it and set the date field with it's initial 'score'
        $created = $this->getMetaKeyValue($val, 'linkoscope_created');
        if (null == $created){
            $time = time();
            $val = $this->setMetaKey($val, 'linkoscope_created', date(DATE_ATOM, $time));
            $link->votes = 0;
        } else {
            $time = date(DATE_ATOM, $created);
        }

        $val['date'] = date(DATE_ATOM, $time - $this->dateOffset + $link->votes * $this->likeFactor);
        return $val;
    }

    private function apiToComments($comments) {
        $result = [];
        foreach ($comments as $c) {
            $result[] = $this->apiToComment($c);
        }
        return $result;
    }

    private function apiToComment($c) {
        return new Comment([
            'id' => $c['ID'],
            'date' => $c['date'],
            'postId' => $c['post->ID'],
            'content' => $c['content'],
            'author' => $c['author->name'],
            'votes' => $c['like_count'],
        ]);
    }

    private function commentToApi(Comment $comment){
        return [
            'content' => $comment->content,
            'date' => $comment->date,
        ];
    }

    private function getMetaKeyValue($result, $key){
        if (!isset($result['metadata']) || !is_array($result['metadata']))
            return null;
        foreach ($result['metadata'] as $data){
            if ($data['key'] == $key)
                return $data['value'];
        }
        return null;
    }

    private function setMetaKey($result, $key, $value){
        $result = $this->deleteMetaKey($result, $key);
        if(!isset($result['metadata']))
            $result['metadata'] = [];
        $result['metadata'][] = ['key' => $key, 'value' => $value];
        return $result;
    }

    private function deleteMetaKey($result, $key){
        if (!isset($result['metadata']) || !is_array($result['metadata']))
            return $result;

        $arr = $result['metadata'];
        $result['metadata'] = [];
        foreach ($arr as $item){
            if (isset($item['key']) && $item['key'] != $key)
                $result['metadata'][] = $item;
        }

        return $result;
    }
}

