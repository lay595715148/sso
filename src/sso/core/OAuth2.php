<?php

namespace sso\core;

use HttpRequest;
use HttpResponse;
use lay\util\Logger;
use sso\model\User;

class OAuth2 {
    const REQUEST_TYPE_CODE = 'code';
    const REQUEST_TYPE_POST = 'post';//用户提交登录
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
     *
     * 生成一个code,此code为唯一值。可以是：授权码、访问令牌、刷新令牌
     *
     * @return string
     */
    public static function generateCode() {
        return md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
    }
    public static function getResponseType(HttpRequest $request, HttpResponse $response) {
        $responseType = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_RESPONSE_TYPE];
        if(in_array($responseType, array(OAuth2::RESPONSE_TYPE_CODE, OAuth2::RESPONSE_TYPE_TOKEN))) {
            return $responseType;
        } else {
            return OAuth2::RESPONSE_TYPE_CODE;
        }
    }
    public static function getRedirectURI(HttpRequest $request, HttpResponse $response) {
        $redirectURI = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_REDIRECT_URI];
        if(empty($redirectURI)) {
            return '';
        } else {
            return $redirectURI;
        }
    }
    public static function getClientId(HttpRequest $request, HttpResponse $response) {
        $clientId = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_CLIENT_ID];
        if(empty($clientId)) {
            return '';
        } else {
            return $clientId;
        }
    }
    /**
     * 
     * @param HttpRequest $request
     * @param HttpResponse $response
     * @return array
     */
    public static function getSessionUser(HttpRequest $request, HttpResponse $response) {
        $id = $_SESSION['userid'];
        $name = $_SESSION['username'];
        $nick = $_SESSION['usernick'];
        if(empty($id) || empty($name)) {
            return false;
        } else {
            $user = new User();
            $user->setId($id);
            $user->setName($name);
            $user->setNick($nick);
            return $user->toArray();
        }
    }
    public static function unsetSessionUser(HttpRequest $request, HttpResponse $response) {
        
    }
    public static function getRequestType(HttpRequest $request, HttpResponse $response) {
        if($_REQUEST[OAuth2::HTTP_QUERY_PARAM_GRANT_TYPE]) {
            $grantType = $_GET[OAuth2::HTTP_QUERY_PARAM_GRANT_TYPE];
        } else {
            $grantType = OAuth2::GRANT_TYPE_AUTHORIZATION_CODE;
        }
        switch($grantType) {
            case OAuth2::GRANT_TYPE_AUTHORIZATION_CODE:
                $requestType = OAuth2::REQUEST_TYPE_TOKEN;
                break;
            case OAuth2::GRANT_TYPE_PASSWORD:
                $requestType = OAuth2::REQUEST_TYPE_PASSWORD;
                break;
            case OAuth2::GRANT_TYPE_REFRESH_TOKEN:
                $requestType = OAuth2::REQUEST_TYPE_REFRESH_TOKEN;
                break;
            default:
                $grantType = OAuth2::GRANT_TYPE_AUTHORIZATION_CODE;
                $requestType = OAuth2::REQUEST_TYPE_TOKEN;
                break;
        }
        return $requestType;
    }
    public static function checkRequest(HttpRequest $request, HttpResponse $response, $requestType = '') {
        if(empty($requestType)) {
            $requestType = OAuth2::REQUEST_TYPE_CODE;
        }
        $responseType = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_RESPONSE_TYPE];
        $grantType = $_POST[OAuth2::HTTP_QUERY_PARAM_GRANT_TYPE];
        $clientId = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_CLIENT_ID];
        $redirectURI = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_REDIRECT_URI];
        $clientSecret = $_POST[OAuth2::HTTP_QUERY_PARAM_CLIENT_SECRET];
        $code = $_POST[OAuth2::HTTP_QUERY_PARAM_CODE];
        $token = $_POST[OAuth2::HTTP_QUERY_PARAM_TOKEN];
        $username = $_POST[OAuth2::HTTP_QUERY_PARAM_USERNAME];
        $password = $_POST[OAuth2::HTTP_QUERY_PARAM_PASSWORD];
        $refreshToken = $_POST[OAuth2::HTTP_QUERY_PARAM_REFRESH_TOKEN];
        $userid = $_POST[OAuth2::HTTP_QUERY_PARAM_USER_ID];
        $id = $_SESSION['userid'];
        $name = $_SESSION['username'];
        $ret = true;
        
        switch($requestType) {
            case OAuth2::REQUEST_TYPE_CODE:
                if(empty($clientId) || empty($redirectURI)) {
                    $ret = false;
                } else if($responseType && $responseType != OAuth2::RESPONSE_TYPE_CODE && $responseType != OAuth2::RESPONSE_TYPE_TOKEN) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_POST:
                // 登录会话用户没有存在session里
                if(empty($clientId) || empty($redirectURI) || ((empty($username) || empty($password)) && (empty($id) || empty($name)))) {
                    $ret = false;
                } else if($responseType && $responseType != OAuth2::RESPONSE_TYPE_CODE && $responseType != OAuth2::RESPONSE_TYPE_TOKEN) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_TOKEN:
                if(empty($clientId) || empty($redirectURI) || empty($clientSecret) || empty($code)) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_PASSWORD:
                if(empty($clientId) || empty($clientSecret) || empty($grantType) || empty($username) || empty($password)) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_REFRESH_TOKEN:
                if(empty($clientId) || empty($clientSecret) || empty($grantType) || empty($refreshToken)) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_SHOW:
                if(empty($token) || empty($userid)) {
                    $ret = false;
                }
                break;
            default:
                $ret = false;
                break;
        }
        return $ret;
    }
}
?>
