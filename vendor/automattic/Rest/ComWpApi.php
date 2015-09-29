<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest;

class ComWpApi extends BaseWpApi {
    public $type;
    public $clientId;
    public $clientSecret;
    public $redirectUrl;
    public $blogId;
    public $blogUrl;

    public $token;

    public $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'LinkoScope WordPress.com Client',
        CURLOPT_SSL_VERIFYPEER => false,
    ];


    protected function requestFilter(ApiRequest $request)
    {
        $request->headers[] = 'Authorization: Bearer ' . $this->token;

        if ($request->body != null)
        {
            if (preg_match('/token$/', $request->url) == 0)
            {
                $request->headers[] = 'Content-type: application/json';
                $request->body = json_encode($request->body);
            }
        }
        return $request;
    }
}