<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-20
 * Time: 2:52 PM
 *
 * This class extends the Yii framework Oauth1 class to better support JSON REST APIs
 */

namespace automattic\Rest;

use yii\authclient\OAuth1;

class JsonOauth1 extends OAuth1
{
    private static $oauthFields = [
        'oauth_consumer_key',
        'oauth_token',
        'oauth_version',
        'oauth_nonce',
        'oauth_timestamp',
        'oauth_signature_method',
        'oauth_signature',
        'oauth_consumer_key',
        'oauth_callback',

    ];

    /**
     * Composes HTTP request CUrl options, which will be merged with the default ones.
     * @param string $method request type.
     * @param string $url request URL.
     * @param array $params request params.
     * @return array CUrl options.
     * @throws Exception on failure.
     */
    protected function composeRequestCurlOptions($method, $url, array $params)
    {
        $oauthFields = array_flip(self::$oauthFields);
        $curlOptions = [];
        switch ($method) {
            case 'GET': {
                $curlOptions[CURLOPT_URL] = $this->composeUrl($url, $params);
                break;
            }
            case 'DELETE': {
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                $curlOptions[CURLOPT_URL] = $this->composeUrl($url, $params);
                break;
            }
            case 'POST': {
                $curlOptions[CURLOPT_POST] = true;
                $curlOptions[CURLOPT_HTTPHEADER] = ['Content-type: application/x-www-form-urlencoded'];
                if (!empty($params)) {
                    $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                }
                $authorizationHeader = $this->composeAuthorizationHeader($params);
                if (!empty($authorizationHeader)) {
                    $curlOptions[CURLOPT_HTTPHEADER][] = $authorizationHeader;
                }
                break;
            }
            case 'PUT': {
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $curlOptions[CURLOPT_POST] = true;
                $curlOptions[CURLOPT_HTTPHEADER] = ['Content-type: application/json'];
                if (!empty($params)) {
                    $curlOptions[CURLOPT_URL] = $this->composeUrl($url, array_intersect_key($params, $oauthFields));
                    $curlOptions[CURLOPT_POSTFIELDS] = json_encode($params['body']);
                }
                $authorizationHeader = $this->composeAuthorizationHeader($params);
                if (!empty($authorizationHeader)) {
                    $curlOptions[CURLOPT_HTTPHEADER][] = $authorizationHeader;
                }
                break;
            }
            case 'HEAD': {
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                if (!empty($params)) {
                    $curlOptions[CURLOPT_URL] = $this->composeUrl($url, $params);
                }
                break;
            }
            default: {
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                if (!empty($params)) {
                    $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                }
            }
        }

        return $curlOptions;
    }

    /**
     * Sign request with [[signatureMethod]].
     * @param string $method request method.
     * @param string $url request URL.
     * @param array $params request params.
     * @return array signed request params.
     */
    protected function signRequest($method, $url, array $params)
    {
        $signatureMethod = $this->getSignatureMethod();
        $params['oauth_signature_method'] = $signatureMethod->getName();
        $filter = array_flip(self::$oauthFields);
        $signatureBaseString = $this->composeSignatureBaseString($method, $url, array_intersect_key($params, $filter));
        $signatureKey = $this->composeSignatureKey();
        $params['oauth_signature'] = $signatureMethod->generateSignature($signatureBaseString, $signatureKey);

        return $params;
    }
}