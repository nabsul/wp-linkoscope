<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-11
 * Time: 9:26 AM
 */

namespace automattic\Rest;

class OrgWpApi extends BaseWpApi
{
    public $type;
    public $consumerKey;
    public $consumerSecret;
    public $accessToken;
    public $accessTokenSecret;
    public $curlOptions = [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_USERAGENT => 'LinkoScope WP-API OAuth 1.0 Client',
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ];

    protected function requestFilter(ApiRequest $req)
    {
        $req->params = $this->signRequest($req->method, $req->url, $req->params);
        $req->headers[] = 'Content-type: application/json';
        $req->headers[] = $this->composeAuthorizationHeader($req->params);
        if ($req->body != null)
            $req->body = json_encode($req->body);
        return $req;
    }

    private function composeAuthorizationHeader(array $params)
    {
        $header = 'Authorization: OAuth';
        $headerParams = [];

        foreach ($params as $key => $value) {
            if (substr_compare($key, 'oauth', 0, 5)) {
                continue;
            }
            $headerParams[] = rawurlencode($key) . '="' . rawurlencode($value) . '"';
        }
        if (!empty($headerParams)) {
            $header .= ' ' . implode(', ', $headerParams);
        }

        return $header;
    }

    private function signRequest($method, $url, array $params)
    {
        $params = array_merge($params, [
            'oauth_version' => '1.0',
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),
            'oauth_consumer_key' => $this->consumerKey,
        ]);
        if ($this->accessToken != null)
            $params['oauth_token'] = $this->accessToken;
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $signatureBaseString = $this->composeSignatureBaseString($method, $this->baseUrl . $url, $params);
        $signatureKey = $this->composeSignatureKey();
        $params['oauth_signature'] = base64_encode(hash_hmac('sha1', $signatureBaseString, $signatureKey, true));

        return $params;
    }

    private function composeSignatureBaseString($method, $url, array $params)
    {
        unset($params['oauth_signature']);
        uksort($params, 'strcmp'); // Parameters are sorted by name, using lexicographical byte value ordering. Ref: Spec: 9.1.1
        $parts = [
            strtoupper($method),
            $url,
            http_build_query($params, '', '&', PHP_QUERY_RFC3986)
        ];
        $parts[2] = str_replace('%5B', '[', $parts[2]);
        $parts[2] = str_replace('%5D', ']', $parts[2]);
        $parts = array_map('rawurlencode', $parts);

        return implode('&', $parts);
    }

    private function composeSignatureKey()
    {
        $signatureKeyParts = [
            $this->consumerSecret
        ];

        $signatureKeyParts[] = $this->accessTokenSecret ?: '';
        $signatureKeyParts = array_map('rawurlencode', $signatureKeyParts);

        return implode('&', $signatureKeyParts);
    }
}

