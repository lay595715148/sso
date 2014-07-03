<?php

/**
 * PHP SDK for SSO (using OAuth2)
 * 
 * @author Lay Li
 */
namespace sso\sdk;

use lay\util\Logger;
/**
 * SSO OAuth 认证类(OAuth2)
 *
 * 授权机制说明请大家参考OAuth官方
 *
 * @author Lay Li
 * @version 1.0
 */
class SsoAuth {
    const REQUEST_TYPE_CODE = 'code';
    const REQUEST_TYPE_POST = 'post'; // 用户提交登录
    const REQUEST_TYPE_TOKEN = 'token';
    const REQUEST_TYPE_PASSWORD = 'password';
    const REQUEST_TYPE_REFRESH_TOKEN = 'refresh_token';
    const REQUEST_TYPE_SHOW = 'show';
    const RESPONSE_TYPE_CODE = 'code';
    const RESPONSE_TYPE_TOKEN = 'token';
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const GRANT_TYPE_PASSWORD = 'password';
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    const CLIENT_TYPE_WEB = 1;
    const CLIENT_TYPE_DESKTOP = 2;
    const CLIENT_TYPE_IMPLICIT = 3;
    const TOKEN_TYPE_ACCESS = 1;
    const TOKEN_TYPE_REFRESH = 2;
    const HTTP_QUERY_PARAM_REQUEST_TYPE = 'request_type';
    const HTTP_QUERY_PARAM_RESPONSE_TYPE = 'response_type';
    const HTTP_QUERY_PARAM_GRANT_TYPE = 'grant_type';
    const HTTP_QUERY_PARAM_CLIENT_ID = 'client_id';
    const HTTP_QUERY_PARAM_REDIRECT_URI = 'redirect_uri';
    const HTTP_QUERY_PARAM_CLIENT_SECRET = 'client_secret';
    const HTTP_QUERY_PARAM_CODE = 'code';
    const HTTP_QUERY_PARAM_TOKEN = 'token';
    const HTTP_QUERY_PARAM_USERNAME = 'username';
    const HTTP_QUERY_PARAM_PASSWORD = 'password';
    const HTTP_QUERY_PARAM_ACCESS_TOKEN = 'token';
    const HTTP_QUERY_PARAM_REFRESH_TOKEN = 'refresh_token';
    const HTTP_QUERY_PARAM_USER_ID = 'user_id';
    /**
     * client id
     * 
     * @var string
     */
    public $clientId;
    /**
     * client secret
     * 
     * @var string
     */
    public $clientSecret;
    /**
     * redirect URI
     * @var string
     */
    public $redirectURI;
    /**
     * access token
     * 
     * @var string
     */
    public $accessToken;
    /**
     * refresh token
     * 
     * @var string
     */
    public $refreshToken;
    /**
     * Set timeout default.
     * 
     * @var int
     */
    public $timeout = 30;
    /**
     * Set connect timeout.
     * 
     * @var int
     */
    public $connectTimeout = 30;
    /**
     * user agent
     * 
     * @var string
     */
    public $userAgent = '';
    /**
     * Verify SSL Cert.
     * 
     * @var boolean
     */
    public $sslVerifyPeer = false;
    /**
     * authorize接口地址
     * 
     * @var string
     */
    public $authorizeURL = 'http://sso.laysoft.cn/authorize';
    /**
     * token接口地址
     * 
     * @var string
     */
    public $tokenURL = 'http://sso.laysoft.cn/token';
    /**
     * 构造方法
     * @param string $clientId
     * @param string $clientSecret
     * @param string $accessToken
     * @param string $refreshToken
     */
    public function __construct($clientId, $clientSecret, $redirectURI, $accessToken = '', $refreshToken = '') {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURI = $redirectURI;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }
    /**
     * 获取完成authorize接口地址参数数据
     *
     * @param string $redirectURI
     *            授权后的回调地址
     * @param string $responseType
     *            支持的值包括 code 和token 默认值为code
     * @param string $scope
     *            申请scope权限所需参数，可一次申请多个scope权限，用逗号分隔。
     * @param string $state
     *            用于保持请求和回调的状态，在回调时，会在Query Parameter中回传该参数。开发者可以用这个参数验证请求有效性，也可以记录用户请求授权页前的位置。这个参数可用于防止跨站请求伪造（CSRF）攻击
     * @return array
     */
    public function getAuthorizeURL($responseType = '', $scope = '', $state = '1q2w3e') {
        $responseType = $responseType ? $responseType : self::RESPONSE_TYPE_CODE;
        $params = array();
        $params['client_id'] = $this->clientId;
        $params['redirect_uri'] = $this->redirectURI;
        $params['response_type'] = $responseType;
        $params['scope'] = $scope;
        $params['state'] = $state;
        return $this->authorizeURL . '?' . http_build_query($params);
    }
    
    /**
     * 请求token接口，获取令牌码
     *
     * @param string $type
     *            请求的类型,可以为:code, token
     * @param array $options
     *            其他参数：
     *            - 当$type为code时： array('code'=>..., 'redirectURI'=>...)
     *            - 当$type为token时： array('refreshToken'=>...)
     * @return array
     */
    public function getToken($type = '', $options = array()) {
        $type = $type ? $type : self::REQUEST_TYPE_CODE;
        $params = array();
        $params['client_id'] = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        if($type === 'token') {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $options['refreshToken'];
        } else if($type === 'code') {
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $options['code'];
            $params['redirect_uri'] = $this->redirectURI;
        }
        
        $response = $this->http($this->tokenURL, 'POST', $params);
        $token = json_decode($response, true);
        
        if(is_array($token) && ! isset($token['error'])) {
            $this->accessToken = $token['access_token'];
            $this->refreshToken = $token['refresh_token'];
        }
        return $token;
    }
    
    /**
     * Make an fopen request
     *
     * @return string API results
     */
    protected function open($url, $fields = null) {
        $path = $url . "?" . ((is_array($fields)) ? http_build_query($fields) : $fields);
        $stream = $this->sslVerifyPeer ? fsockopen($path, 'r') : fopen($path, 'r');
        $response = stream_get_contents($stream);
        return $response;
    }
    /**
     * Make an HTTP request
     *
     * @return string API results
     */
    protected function http($url, $method, $fields = null, $headers = null) {
        if(! function_exists('curl_init'))
            exit('{"success":false,"msg":"install curl"}');
        $ci = curl_init();
        
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
        
        switch($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if(! empty($fields))
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $fields);
                break;
            case 'GET':
                if(! empty($fields))
                    $url = $url . "?" . (is_array($fields) ? http_build_query($fields) : $fields);
                break;
        }
        
        if(isset($this->accessToken) && $this->accessToken)
            $headers[] = "Authorization: OAuth2 " . $this->accessToken;
        
        $headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
        
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_URL, $url);
        
        $response = curl_exec($ci);
        curl_close($ci);
        
        return $response;
    }
    public function toSsoClient() {
        return new SsoClient($this->clientId, $this->clientSecret, $this->redirectURI, $this->accessToken, $this->refreshToken);
    }
}
?>
