<?php

namespace sso\core;

use HttpRequest;
use HttpResponse;
use lay\util\Logger;

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
    const CLIENT_TYPE_WEB = 'web';
    const CLIENT_TYPE_DESKTOP = 'desktop';
    const CLIENT_TYPE_IMPLICIT = 'implicit';
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
        $responseType = $_GET[OAuth2::HTTP_QUERY_PARAM_RESPONSE_TYPE];
        $grantType = $_POST[OAuth2::HTTP_QUERY_PARAM_GRANT_TYPE];
        $clientID = $_GET[OAuth2::HTTP_QUERY_PARAM_CLIENT_ID];
        $clientID = $clientID ? $clientID : $_POST[OAuth2::HTTP_QUERY_PARAM_CLIENT_ID];
        $redirectURI = $_GET[OAuth2::HTTP_QUERY_PARAM_REDIRECT_URI];
        $redirectURI = $redirectURI ? $redirectURI : $_POST[OAuth2::HTTP_QUERY_PARAM_REDIRECT_URI];
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
                if(empty($clientID) || empty($redirectURI)) {
                    $ret = false;
                } else if($responseType && $responseType != OAuth2::RESPONSE_TYPE_CODE && $responseType != OAuth2::RESPONSE_TYPE_TOKEN) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_POST:
                // 登录会话用户没有存在session里
                if(empty($clientID) || empty($redirectURI) || ((empty($username) || empty($password)) && (empty($id) || empty($name)))) {
                    $ret = false;
                } else if($responseType && $responseType != OAuth2::RESPONSE_TYPE_CODE && $responseType != OAuth2::RESPONSE_TYPE_TOKEN) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_TOKEN:
                if(empty($clientID) || empty($redirectURI) || empty($clientSecret) || empty($code)) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_PASSWORD:
                if(empty($clientID) || empty($clientSecret) || empty($grantType) || empty($username) || empty($password)) {
                    $ret = false;
                }
                break;
            case OAuth2::REQUEST_TYPE_REFRESH_TOKEN:
                if(empty($clientID) || empty($clientSecret) || empty($grantType) || empty($refreshToken)) {
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
