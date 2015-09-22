<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest\Com;


use automattic\Rest\iWpApi;
use yii\base\Object;
use automattic\Rest\Models\Link;
use automattic\Rest\Models\Comment;
use yii\authclient\OAuth2;

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

    public function authorize($returnUrl){
        return $this->authorizeUrl .
            "?client_id=$this->clientId&redirect_uri=$returnUrl&response_type=code";
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
        $this->blogId = $secret->blog_id;
        $this->blogUrl = $secret->blog_url;
        $this->token = $secret->access_token;
        return true;
    }

    public function access($token, $verifier){}
    public function getLinks(){}
    public function getLink($id){}
    public function addLink(Link $link){}
    public function updateLink(Link $link){}
    public function deleteLink($id){}
    public function getTypes(){}
    public function getAccount(){}
    public function getComments($postId){}
    public function addComment(Comment $comment){}
}