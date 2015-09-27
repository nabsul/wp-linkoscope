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
use yii\base\InvalidParamException;
use Yii;
use yii\log\Logger;

class JsonOauth1 extends OAuth1
{
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
                throw new InvalidParamException("Unknown http method: $method");
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
        Yii::getLogger()->log('sign params: ' . json_encode($params), Logger::LEVEL_INFO);
        $signatureMethod = $this->getSignatureMethod();
        $params['oauth_signature_method'] = $signatureMethod->getName();
        $signatureBaseString = $this->composeSignatureBaseString($method, $url, array_diff_key($params, ['body' => 1]));
        $signatureKey = $this->composeSignatureKey();
        $params['oauth_signature'] = $signatureMethod->generateSignature($signatureBaseString, $signatureKey);

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
}