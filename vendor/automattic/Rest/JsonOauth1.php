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

use yii\base\Object;
use Yii;
use yii\web\HttpException;

/**
 * Class JsonOauth1
 *
 * @package automattic\Rest
 *
*/
class JsonOauth1 extends Object
{
    public $consumerKey;
    public $consumerSecret;
    public $authUrl;
    public $requestTokenUrl;
    public $accessTokenUrl;
    public $curlOptions;

    public $accessToken;
    public $accessTokenSecret;

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
        $curlOptions = [];
        switch ($method) {
            case 'GET':
            case 'HEAD':
            case 'DELETE':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                $curlOptions[CURLOPT_URL] = $this->composeUrl($url, $params);
                break;
           case 'POST':
            case 'PUT':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                $curlOptions[CURLOPT_POST] = true;
                $curlOptions[CURLOPT_HTTPHEADER] = ['Content-type: application/json'];
                if (!empty($params)) {
                    $curlOptions[CURLOPT_URL] = $this->composeUrl($url, array_diff_key($params, ['body' => 1]));
                    $curlOptions[CURLOPT_POSTFIELDS] = json_encode($params['body']);
                }
                $authorizationHeader = $this->composeAuthorizationHeader($params);
                if (!empty($authorizationHeader)) {
                    $curlOptions[CURLOPT_HTTPHEADER][] = $authorizationHeader;
                }
                break;
            default:
                throw new \Exception("Unknown http method: $method");
        }

        return $curlOptions;
    }

    /**
     * Composes URL from base URL and GET params.
     * @param string $url base URL.
     * @param array $params GET params.
     * @return string composed URL.
     */
    protected function composeUrl($url, array $params = [])
    {
        return $url . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Composes authorization header content.
     * @param array $params request params.
     * @param string $realm authorization realm.
     * @return string authorization header content.
     */
    protected function composeAuthorizationHeader(array $params, $realm = '')
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

    /**
     * Sign request with [[signatureMethod]].
     * @param string $method request method.
     * @param string $url request URL.
     * @param array $params request params.
     * @return array signed request params.
     */
    protected function signRequest($method, $url, array $params)
    {
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $signatureBaseString = $this->composeSignatureBaseString($method, $url, array_diff_key($params, ['body' => 1]));
        $signatureKey = $this->composeSignatureKey();
        $params['oauth_signature'] = base64_encode(hash_hmac('sha1', $signatureBaseString, $signatureKey, true));

        return $params;
    }

    /**
     * Creates signature base string, which will be signed by [[signatureMethod]].
     * @param string $method request method.
     * @param string $url request URL.
     * @param array $params request params.
     * @return string base signature string.
     */
    protected function composeSignatureBaseString($method, $url, array $params)
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

    /**
     * Composes request signature key.
     * @return string signature key.
     */
    protected function composeSignatureKey()
    {
        $signatureKeyParts = [
            $this->consumerSecret
        ];

        $signatureKeyParts[] = $this->accessTokenSecret ?: '';
        $signatureKeyParts = array_map('rawurlencode', $signatureKeyParts);

        return implode('&', $signatureKeyParts);
    }

    public function api($url, $method = 'GET', array $params = [], array $headers = [])
    {
        $params['oauth_consumer_key'] = $this->consumerKey;
        $params['oauth_token'] = $this->accessToken;
        return $this->sendSignedRequest($method, $url, $params, $headers);
    }

    public function sendSignedRequest($method, $url, array $params = [], array $headers = [])
    {
        $params = array_merge($params, [
            'oauth_version' => '1.0',
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),
        ]);
        $params = $this->signRequest($method, $url, $params);

        return $this->sendRequest($method, $url, $params, $headers);
    }

    protected function sendRequest($method, $url, array $params = [], array $headers = [])
    {
        $curlOptions = $this->composeRequestCurlOptions(strtoupper($method), $url, $params)
            + [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => Yii::$app->name . ' OAuth 1.0 Client',
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
            ]
            + $this->curlOptions;

        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }
        $response = curl_exec($curlResource);
        $responseHeaders = curl_getinfo($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new HttpException(500, 'Curl error requesting "' .  $url . '": #' . $errorNumber . ' - ' . $errorMessage);
        }

        if (strncmp($responseHeaders['http_code'], '20', 2) !== 0) {
            throw new HttpException($responseHeaders['http_code'], $response);
        }

        $result = json_decode($response, true);
        if ($result == null)
            parse_str($response, $result);
        return $result;
    }
}
