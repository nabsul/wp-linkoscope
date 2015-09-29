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

class OrgLinkoScope
{
    public $type = 'org';
    private $linkEndpoint = 'linkolink';
    private $api;
    private $linkVoteMultiplier = 24 * 60 * 60;
    private $commentVoteMultiplier = 24 * 60 * 60;

    public function __construct(OrgWpApi $api) {
        $this->api = $api;
    }

    public function getConfig()
    {
        return $this->api->getConfig();
    }

    public function authorize($returnUrl) {
        return $this->api->getAuthorizeUrl($returnUrl);
    }

    public function access($token, $verifier) {
        return $this->api->getAccessToken($token, $verifier);
    }

    public function getLinks() {
        $sortParams = [
            'filter' => [
                'meta_key' => 'linkoscope_score',
                'order' => 'DESC',
                'orderby' => 'meta_value_num',
        ]];

        $links = $this->api->listCustom($this->linkEndpoint,$sortParams);
        return $this->apiToLinks($links);
    }

    public function getLink($id) {
        $link = $this->api->getCustom($this->linkEndpoint, $id);
        return $this->apiToLink($link);
    }

    public function addLink(Link $link)
    {
        $body = $this->linkToApi($link);
        return $this->api->addCustom($this->linkEndpoint, $body);
    }

    public function updateLink(Link $link)
    {
        $link->score = strtotime($link->date) + $this->linkVoteMultiplier * count($link->votes);
        $body = $this->linkToApi($link);
        $result = $this->api->updateCustom($this->linkEndpoint, $link->id, $body);
        return $this->apiToLink($result);
    }

    public function likeLink($id, $userId)
    {
        $link = $this->getLink($id);
        $link->votes[] = $id;
        //$link->votes = array_unique($link->votes);  //TODO: Uncomment this after testing is complete
        return $this->updateLink($link);
    }

    public function unlikeLink($id)
    {
        $link = $this->getLink($id);
        $link->votes = array_diff($link->votes, [$id]);
        return $this->api->updateCustom('linkolink', $id, $link);
    }

    public function deleteLink($id)
    {
        return $this->api->deleteCustom($this->linkEndpoint, $id);
    }

    public function getTypes()
    {
        return $this->api->listTypes();
    }

    public function getAccount()
    {
        return $this->api->getSelf();
    }

    public function getComments($postId)
    {
        $sort = ['orderby' => 'karma'];
        $results = $this->api->listComments($postId, $sort);
        return $this->apiToComments($results);
    }

    public function getComment($id)
    {
        $c = $this->api->getComment($id);
        return $this->apiToComment($c);
    }

    public function addComment(Comment $comment)
    {
        $body = $this->commentToApi($comment);
        return $this->api->addComment($body);
    }

    public function updateComment(Comment $comment)
    {
        $comment->score = strtotime($comment->date) +
            $this->commentVoteMultiplier * count($comment->likes);
        $body = $this->commentToApi($comment);
        return $this->api->updateComment($comment->id, $body);
    }

    public function likeComment($id, $userId)
    {
        $comment = $this->getComment($id);
        $comment->likes[] = $userId;
        return $this->updateComment($comment);
    }

    public function unlikeComment($id, $userId)
    {
        $comment = $this->getComment($id);
        $comment->likes = array_diff_key($comment->likes, [$userId]);
        return $this->updateComment($comment);
    }

    public function deleteComment($id)
    {
        return $this->api->deleteComment($id);
    }

    private function apiToLinks($items) {
        $result = [];
        foreach ($items as $item)
            $result[] = $this->apiToLink($item);
        return $result;
    }

    private function apiToLink($item)
    {
        return new Link([
            'id' => $item['id'],
            'date' => $item['date'],
            'title' => $item['title']['raw'],
            'url' => $item['content']['raw'],
            'score' => $item['linkoscope_score'] ?: 0,
            'votes' => empty($item['linkoscope_likes']) ? [] : explode(';', $item['linkoscope_likes']),
        ]);
    }

    private function linkToApi(Link $link)
    {
        return [
            'title' => $link->title,
            'content' => $link->title,
            'linkoscope_score' => $link->score,
            'linkoscope_likes' => implode(';', $link->votes),
        ];
    }

    private function apiToComments($items){
        $result = [];
        foreach ($items as $item)
            $result[] = $this->apiToComment($item);
        return $result;
    }

    private function apiToComment($c){
        return new Comment([
            'id' => $c['id'],
            'date' => $c['date'],
            'postId' => $c['post'],
            'content' => $c['content']['raw'],
            'likes' => empty($c['linkoscope_likes']) ? [] : explode(';', $c['linkoscope_likes']),
            'score' => $c['karma'],
            'author' => $c['author_name'],
        ]);
    }

    private function commentToApi(Comment $comment){
        return [
            'post' => $comment->postId,
            'content' => $comment->content,
            'author_name' => $comment->author,
            'karma' =>  $comment->score,
            'linkoscope_likes' => implode(';', $comment->likes),
        ];
    }
}