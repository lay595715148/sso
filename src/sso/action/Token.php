<?php
namespace sso\action;

use lay\action\JSONAction;
use lay\action\TypicalAction;
use sso\core\OAuth2;
use sso\service\OAuth2TokenService;
use sso\service\OAuth2CodeService;
use sso\service\ClientService;
use lay\App;
use lay\util\Logger;
use sso\service\ScopeService;

class Token extends UAction {
    /**
     * ClientService
     * 
     * @var ClientService
     */
    protected $clientService;
    /**
     * ScopeService
     *
     * @var ScopeService
     */
    protected $scopeService;
    /**
     * OAuth2CodeService
     * 
     * @var OAuth2CodeService
     */
    protected $oauth2CodeService;
    /**
     * OAuth2TokenService
     * 
     * @var OAuth2TokenService
     */
    protected $oauth2TokenService;
    public function onCreate() {
        $this->clientService = $this->service('sso\service\ClientService');
        $this->scopeService = $this->service('sso\service\ScopeService');
        $this->oauth2CodeService = $this->service('sso\service\OAuth2CodeService');
        $this->oauth2TokenService = $this->service('sso\service\OAuth2TokenService');
        parent::onCreate();
    }
    public function onGet() {
        $_POST = $_REQUEST;
        $this->onPost();
    }
    public function onPost() {
        $request = $this->request;
        $response = $this->response;

        $userid = $_POST[OAuth2::HTTP_QUERY_PARAM_USER_ID];
        $userid = $userid ? $userid : false;
        $username = $_POST[OAuth2::HTTP_QUERY_PARAM_USERNAME];
        $username = $username ? $username : false;
        $password = $_POST[OAuth2::HTTP_QUERY_PARAM_PASSWORD];
        $password = $password ? $password : '';
        $code = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_CODE];
        $refreshToken = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_REFRESH_TOKEN];
        $useRefresh = App::get('oauth2.use_refresh_token', true);

        $scope = OAuth2::getRequestScope($request, $response);
        $requestType = OAuth2::getRequestType($request, $response);
        //$sUser = OAuth2::getSessionUser($request, $response);
        $responseType = OAuth2::getResponseType($request, $response);
        $clientId = OAuth2::getClientId($request, $response);
        $clientType = $responseType == OAuth2::RESPONSE_TYPE_TOKEN ? OAuth2::CLIENT_TYPE_IMPLICIT : OAuth2::CLIENT_TYPE_WEB;
        $redirectURI = OAuth2::getRedirectURI($request, $response);
        $clientSecret = OAuth2::getClientSecret($request, $response);
        $check = OAuth2::checkRequest($request, $response, $requestType);
        $clientType = $requestType == OAuth2::REQUEST_TYPE_TOKEN ? OAuth2::CLIENT_TYPE_WEB : ( $requestType == OAuth2::REQUEST_TYPE_PASSWORD ? OAuth2::CLIENT_TYPE_DESKTOP : false );
        
        if($check) {
            $client = $this->clientService->checkClient($clientId, $clientType, $redirectURI, $clientSecret);
            if($client) {
                if($requestType == OAuth2::REQUEST_TYPE_TOKEN && $code) {
                    $oauth2code = $this->oauth2CodeService->get($code);
                    if($oauth2code && $oauth2code['clientId'] == $clientId) {
                        list($scopeStr, $scopeArr) = $this->scopeService->filter($oauth2code['scope']);
                        $user = $this->userService->get($oauth2code['userid']);
                        $params = $this->genTokenParam($user, $client, $scopeStr);
                        $this->template->push($params);
                    } else {
                        $this->errorResponse('invalid_grant');
                    }
                } else if($requestType == OAuth2::REQUEST_TYPE_PASSWORD && $password) {
                    $user = $this->userService->checkUser($password, $userid, $username);
                    if($user) {
                        list($scopeStr, $scopeArr) = $this->scopeService->filter($scope);
                        $this->updateSessionUser($user);
                        $params = $this->genTokenParam($user, $client, $scopeStr);
                        $this->template->push($params);
                    } else {
                        $this->errorResponse('invalid_grant');
                    }
                } else if($requestType == OAuth2::REQUEST_TYPE_REFRESH_TOKEN && $refreshToken) {
                    $oauth2token = $this->oauth2TokenService->get($refreshToken);
                    if($oauth2token && $oauth2token['type'] == OAuth2::TOKEN_TYPE_REFRESH) {
                        list($scopeStr, $scopeArr) = $this->scopeService->filter($oauth2token['scope']);
                        $user = $this->userService->get($oauth2token['userid']);
                        $params = $this->genAccessTokenParam($user, $client, $scopeStr);
                        $this->template->push($params);
                    } else {
                        $this->errorResponse('invalid_grant');
                    }
                } else {
                    $this->errorResponse('invalid_grant');
                }
            } else {
                $this->errorResponse('invalid_client');
            }
        } else {
            $this->errorResponse('invalid_request');
        }
    }
    
    protected function genTokenParam($user, $client, $scope) {
        $scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $params = $this->genAccessTokenParam($user, $client, $scope);
        if(App::get('use_refresh_token', true)) {
            $p = $this->genRefreshTokenParam($user, $client, $scope);
            $params = array_merge($params, $p);
        }
        return $params;
    }
    protected function genAccessTokenParam($user, $client, $scope) {
        $redirectURI = $client['redirectURI'];
        $scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $accessToken = $this->oauth2TokenService->gen($user, $client, $scope);
        $params = array();
        if($accessToken) {
            $params['userid'] = $accessToken['userid'];
            $params['token'] = $accessToken['token'];
            $params['expires'] = $accessToken['expires'];
        }
        return $params;
    }
    protected function genRefreshTokenParam($user, $client, $scope) {
        $redirectURI = $client['redirectURI'];
        $scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $refreshToken = $this->oauth2TokenService->genRefresh($user, $client, $scope);
        $params = array();
        if($refreshToken) {
            $params['refresh_token'] = $refreshToken['token'];
            $params['refresh_expires'] = $refreshToken['expires'];
        }
        return $params;
    }
}
?>
