<?php

namespace sso\action;

use lay\App;
use lay\action\JSONAction;
use lay\util\Logger;
use sso\core\OAuth2;
use sso\service\ClientService;
use lay\util\Collector;
use lay\action\TypicalAction;
use sso\service\UserService;
use sso\service\OAuth2CodeService;
use sso\service\OAuth2TokenService;
use sso\service\ScopeService;

class Authorize extends UAction {
    protected $showJson = true;
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
        $this->clientService = ClientService::getInstance();
        $this->scopeService = ScopeService::getInstance();
        $this->oauth2CodeService = OAuth2CodeService::getInstance();
        $this->oauth2TokenService = OAuth2TokenService::getInstance();
        $this->template->file('authorize.php');
        parent::onCreate();
    }
    public function onGet() {
        $request = $this->request;
        $response = $this->response;
        // $this->scopeService->add(array('id' => 1000, 'description' => '获得您的昵称、头像、性别'));
        // $this->scopeService->add(array('id' => 1001, 'description' => '读取、发表微博信息'));
        // $this->scopeService->upd(1001, array('name' => 'status', 'description' => '读取微博信息'));
        // $this->scopeService->add(array('id' => 1002, 'name' => 'write', 'description' => '发表微博信息'));
        // $this->scopeService->upd(1000, array('name' => 'info'));
        // $this->scopeService->upd(1001, array('name' => 'status'));
        // $this->scopeService->del(1002);
         $this->oauth2CodeService->clean();
        // $this->oauth2CodeService->expire();
         $this->oauth2TokenService->clean();
        // $this->oauth2TokenService->expire();
        // $ret = $this->scopeService->update(array('_id' => array('$gt' => 0)), array('basis' => 1));
        // $ret = $this->scopeService->upd(1000, array('basis' => 1));
        
        // $this->service('sso\service\SessionService')->remove(array('id' => session_id(), 'data' => '', 'expires' => time() + 8400));
        // $this->service('sso\service\SessionService')->add(array('id' => session_id(), 'data' => '', 'expires' => time() + 8400));
        // $ret = $this->clientService->upd(52, array('redirectURI' => 'http://sso.laysoft.cn/redirect', 'clientType' => 1));
        // $ret = $this->clientService->upd(53, array('redirectURI' => 'http://sso.laysoft.cn/redirect', 'clientType' => 3));
        // $ret = $this->clientService->update(array('_id' => array('$gt' => 0)), array('scope' => '1000,1001'));
        // Logger::debug($ret);
        // $ret = $this->clientService->get(50);
        // $ret = $this->clientService->add(array("clientId" => "lay45113", "clientName" => "lay", "clientSecret" => "2b53761249254ce6b502f521e5cc0683", "clientType" => 2,'redirectURI' => 'http://sso.laysoft.cn/redirect', 'clientType' => 2, "location" => "", "description" => "", "icon" => ""));
        // $useRefresh = App::get('oauth2.use_refresh_token', true);
        
        $scope = OAuth2::getRequestScope($request, $response);
        $sUser = OAuth2::getSessionUser($request, $response);
        $responseType = OAuth2::getResponseType($request, $response);
        $clientId = OAuth2::getClientId($request, $response);
        $clientType = $responseType == OAuth2::RESPONSE_TYPE_TOKEN ? OAuth2::CLIENT_TYPE_IMPLICIT : OAuth2::CLIENT_TYPE_WEB;
        $redirectURI = OAuth2::getRedirectURI($request, $response);
        $check = OAuth2::checkRequest($request, $response);
        
        if($check) {
            $client = $this->clientService->checkClient($clientId, $clientType, $redirectURI);
            if($client) {
                if($sUser) {
                    $user = $this->userService->get($sUser['id']);
                    $this->template->push('user', $user);
                } else {
                    $isVerify = $this->isNeedVerification();
                    $this->template->push('is_verify', $isVerify);
                }
                list($scopeStr, $scopeArr) = $this->scopeService->filter($scope);
                // $scopeArr = array_map('intval', explode(',', $client['scope']));
                // $scopes = $this->scopeService->getList($scopeArr);
                // 先标记为不输出JSON格式，以HTML输出
                $this->showJson = false;
                // push一些数据
                $this->template->push('scope', $scopeArr);
                $this->template->push('client', $client);
                $this->template->push('login_type', 'authorize');
                $this->template->push('response_type', $responseType);
                $this->template->push('client_id', $clientId);
                $this->template->push('redirect_uri', $redirectURI);
                $this->template->push('title', 'Authorize');
            } else {
                $this->errorResponse('invalid_client');
            }
        } else {
            $this->errorResponse('invalid_request');
        }
    }
    public function onPost() {
        $request = $this->request;
        $response = $this->response;
        /**
         * 检测使用其他账号登录
         */
        $other = $_REQUEST['otherlogin'];
        $register = $_REQUEST['register'];
        $state = $_REQUEST[OAuth2::HTTP_QUERY_PARAM_STATE];
        $loginCount = $_SESSION['loginCount'];
        $requestType = OAuth2::REQUEST_TYPE_POST;
        $userid = $_POST[OAuth2::HTTP_QUERY_PARAM_USER_ID];
        $userid = $userid ?  : false; // false表示不使用此字段作为验证条件
        $username = $_POST[OAuth2::HTTP_QUERY_PARAM_USERNAME];
        $username = $username ?  : false; // false表示不使用此字段作为验证条件
        $password = $_POST[OAuth2::HTTP_QUERY_PARAM_PASSWORD];
        $password = $password ?  : '';
        $verifyCode = $_POST[OAuth2::HTTP_QUERY_PARAM_VERIFY_CODE];
        $verifyCode = $verifyCode ?  : '';
        
        $scope = OAuth2::getRequestScope($request, $response);
        $sUser = OAuth2::getSessionUser($request, $response);
        $responseType = OAuth2::getResponseType($request, $response);
        $clientId = OAuth2::getClientId($request, $response);
        $clientType = $responseType == OAuth2::RESPONSE_TYPE_TOKEN ? OAuth2::CLIENT_TYPE_IMPLICIT : OAuth2::CLIENT_TYPE_WEB;
        $redirectURI = OAuth2::getRedirectURI($request, $response);
        $check = OAuth2::checkRequest($request, $response, $requestType);
        
        if($other) {
            // 清除验证码,同时也清空登录失败次数
            $this->removeVerifyCode();
            // 清除seesion memcache user,清除cookie
            $this->removeSessionUser();
            $params = array(
                    OAuth2::HTTP_QUERY_PARAM_CLIENT_ID => $clientId,
                    OAuth2::HTTP_QUERY_PARAM_RESPONSE_TYPE => $responseType,
                    OAuth2::HTTP_QUERY_PARAM_REDIRECT_URI => $redirectURI,
                    OAuth2::HTTP_QUERY_PARAM_SCOPE => $scope,
                    OAuth2::HTTP_QUERY_PARAM_STATE => $state
            );
            // 跳转至认证页
            $this->template->redirect($this->name, $params);
        } else if($register) {
            $this->template->redirect(App::get('urls.register', $this->name));
        } else if($check) {
            // 检测客户端信息
            $client = $this->clientService->checkClient($clientId, $clientType, $redirectURI);
            if($client) {
                list($scopeStr, $scopeArr) = $this->scopeService->filter($scope);
                if($sUser) {
                    $user = $this->userService->get($sUser['id']);
                    if($responseType == OAuth2::RESPONSE_TYPE_TOKEN) {
                        // 生成token
                        $params = $this->genTokenParam($user, $client, $scopeStr);
                        $params = array_merge($params, array(
                                'state' => $state
                        ));
                        $this->template->redirect($redirectURI . '#' . http_build_query($params));
                    } else {
                        // 生成code
                        $params = $this->genCodeParam($user, $client, $scopeStr);
                        $params = array_merge($params, array(
                                'state' => $state
                        ));
                        $this->template->redirect($redirectURI, $params);
                    }
                } else {
                    // 打开检测 登录用户任务
                    $user = $this->userService->checkUser(md5($password), $userid, $username);
                    if($user && $this->checkVerifyCode($verifyCode)) {
                        // 清除验证码,同时也清空登录失败次数
                        $this->removeVerifyCode();
                        // 更新SESSION
                        $this->updateSessionUser($user);
                        if($responseType == OAuth2::RESPONSE_TYPE_TOKEN) {
                            // 生成token
                            $params = $this->genTokenParam($user, $client, $scopeStr);
                            $params = array_merge($params, array(
                                    'state' => $state
                            ));
                            $this->template->redirect($redirectURI . '#' . http_build_query($params));
                        } else {
                            // 生成code
                            $params = $this->genCodeParam($user, $client, $scopeStr);
                            $params = array_merge($params, array(
                                    'state' => $state
                            ));
                            $this->template->redirect($redirectURI, $params);
                        }
                    } else {
                        //$this->template->push('user', $user);
                        // 更新登录失败次数
                        $count = $this->updateLoginCount();
                        $isVerify = $this->isNeedVerification();
                        // 错误信息
                        $error = $user ? '验证码错误' : '用户名密码错误';
                        
                        $this->template->push('login_count', $count);
                        $this->template->push('is_verify', $isVerify);
                        // 先标记为不输出JSON格式，以HTML输出
                        $this->showJson = false;
                        // 重新登录，可做
                        $this->template->push('error', $error);
                        $this->template->push('scope', $scopeArr);
                        $this->template->push('login_type', 'authorize');
                        $this->template->push('client', $client);
                        $this->template->push('response_type', $responseType);
                        $this->template->push('client_id', $clientId);
                        $this->template->push('redirect_uri', $redirectURI);
                        $this->template->push('title', 'Authorize');
                    }
                }
            } else {
                $this->errorResponse('invalid_client');
            }
        } else {
            $this->errorResponse('invalid_request');
        }
    }
    public function onStop() {
        if($this->showJson) {
            parent::onStop();
        } else {
            $this->template->display();
        }
    }
    protected function genTokenParam($user, $client, $scope) {
        //$scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $params = $this->genAccessTokenParam($user, $client, $scope);
        if(App::get('use_refresh_token', true)) {
            $p = $this->genRefreshTokenParam($user, $client, $scope);
            $params = array_merge($params, $p);
        }
        return $params;
    }
    protected function genAccessTokenParam($user, $client, $scope) {
        $redirectURI = $client['redirectURI'];
        //$scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $accessToken = $this->oauth2TokenService->gen($user, $client, $scope);
        $params = array();
        if($accessToken) {
            $params[OAuth2::HTTP_QUERY_PARAM_USER_ID] = $accessToken['userid'];
            $params[OAuth2::HTTP_QUERY_PARAM_ACCESS_TOKEN] = $accessToken['token'];
            $params[OAuth2::HTTP_QUERY_PARAM_ACCESS_TOKEN_EXPIRES] = $accessToken['expires'];
        }
        return $params;
    }
    protected function genRefreshTokenParam($user, $client, $scope) {
        //$scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $refreshToken = $this->oauth2TokenService->genRefresh($user, $client, $scope);
        $params = array();
        if($refreshToken) {
            $params[OAuth2::HTTP_QUERY_PARAM_REFRESH_TOKEN] = $refreshToken['token'];
            // $params['refresh_expires'] = $refreshToken['expires'];
        }
        return $params;
    }
    protected function genCodeParam($user, $client, $scope) {
        //$scope = is_string($scope) ? : (is_array($scope)?implode(',', array_keys($scope)):'');
        $oauth2code = $this->oauth2CodeService->gen($user, $client, $scope);
        $params = array();
        if(is_array($oauth2code)) {
            $params[OAuth2::HTTP_QUERY_PARAM_CODE] = $oauth2code['code'];
        } else if(is_string($oauth2code)) {
            $params[OAuth2::HTTP_QUERY_PARAM_CODE] = $oauth2code;
        }
        return $params;
    }
}
?>
