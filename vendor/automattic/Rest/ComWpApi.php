<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest;


use automattic\Rest\iWpApi;
use yii\base\Object;
use automattic\Rest\Models\Link;
use automattic\Rest\Models\Comment;
use yii\authclient\OAuth2;
use yii\web\HttpException;

class ComWpApi extends Object implements  iWpApi {
    public $type;
    public $clientId;
    public $clientSecret;
    public $redirectUrl;
    public $blogId;
    public $blogUrl;

    public $token;

    private $requestUrl = 'https://public-api.wordpress.com/oauth2/token';
    private $authorizeUrl = 'https://public-api.wordpress.com/oauth2/authorize';
    private $authenticateUrl = 'https://public-api.wordpress.com/oauth2/authenticate';

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

    public function authorize($returnUrl = null){
        return
            $this->authorizeUrl .
            "?client_id=$this->clientId&redirect_uri=$this->redirectUrl&response_type=code" .
            ($this->blogId !== null ? "&blog=$this->blogId" : '');
    }

    public function token($code)
    {
        $curl = curl_init( 'https://public-api.wordpress.com/oauth2/token' );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ) );

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false);
        $auth = curl_exec( $curl );

        if ($auth == false) {
            return curl_error($curl);
        }

        $secret = json_decode($auth);
        return $secret;
    }

    public function access($token, $verifier){}

    public function getLinks(){
        $result = $this->get("sites/$this->blogId/posts");
        if (!isset($result->posts))
            return [];

        return ($this->convertPosts($result->posts));
    }

    public function getLink($id){
        $result = $this->get("sites/$this->blogId/posts/$id");
        return $this->convertPosts([$result])[0];
    }

    public function addLink(Link $link){
        return $this->post("sites/$this->blogId/posts/new",
            [
                'title' => $link->title,
                'content' => $link->url,
            ]
        );
    }

    public function updateLink(Link $link){
        return $this->put("sites/$this->blogId/posts/$link->id",
            [
                'title' => $link->title,
                'content' => $link->url,
            ]
        );
    }

    public function likeLink($id)
    {
        return $this->post("sites/$this->blogId/posts/$id/likes/new");
    }

    public function unlikeLink($id)
    {
        return $this->post("sites/$this->blogId/posts/$id/likes/mine/delete");
    }

    public function likeComment($id)
    {
        return $this->post("sites/$this->blogId/comments/$id/likes/new");
    }

    public function unlikeComment($id)
    {
        return $this->post("sites/$this->blogId/comments/$id/likes/mine/delete");
    }

    public function deleteLink($id){
        return $this->post("sites/$this->blogId/posts/$id/delete");
    }

    public function getTypes(){}

    public function getAccount(){
        return $this->get('me');
    }

    public function getComments($postId){
        $result = $this->get("sites/$this->blogId/posts/$postId/replies/");
        if (!isset($result->comments))
            return [];
        return $this->convertComments($result->comments);
    }

    public function addComment(Comment $comment) {
        return $this->post("sites/$this->blogId/posts/$comment->postId/replies/new",
            [
                'content' => $comment->content,
            ]
        );
    }

    public function deleteComment($id)
    {
        return $this->post("sites/$this->blogId/comments/$id/delete");
    }

    private function get($url)
    {
        return $this->send('GET', $url);
    }

    private function delete($url)
    {
        return $this->send('DELETE', $url);
    }

    private function post($url, $body = null)
    {
        return $this->send('POST', $url, $body);
    }

    private function put($url, $body = null)
    {
        return $this->send('PUT', $url, $body);
    }

    private function send($method, $url, $body = null)
    {
        $curl = curl_init( 'https://public-api.wordpress.com/rest/v1.1/' . $url );
        $header = ['Authorization: Bearer ' . $this->token];
        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($body !== null)
        {
            $header[] = 'Content-type: application/json';
            curl_setopt( $curl, CURLOPT_POST, true);
            curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode($body));
        }

        curl_setopt( $curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec( $curl );
        if ($result === false)
            throw new HttpException(500, "API call failed with error: " . curl_error($curl));

        return json_decode($result);
    }

    private function convertPosts($posts)
    {
        $result = [];
        foreach ($posts as $p)
        {
            $result[] = new Link([
                'id' => $p->ID,
                'title' => $p->title,
                'url' => $p->content,
                'votes' => $p->like_count,
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