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

class Authorize extends UAction {
    protected $showJson = true;
    /**
     * ClientService
     * 
     * @var ClientService
     */
    protected $clientService;
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
        $this->oauth2CodeService = $this->service('sso\service\OAuth2CodeService');
        $this->oauth2TokenService = $this->service('sso\service\OAuth2TokenService');
        parent::onCreate();
    }
    public function onGet() {
        $request = $this->request;
        $response = $this->response;
        // $this->clientService->mongo();
        // $this->clientService->mysql();
        // $this->clientService->memcache();
        // $ret = $this->clientService->upd(52, array('redirectURI' => 'http://sso.laysoft.cn/redirect', 'clientType' => 1));
        // Logger::debug($ret);
        // $ret = $this->clientService->get(50);
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
                }
                $this->template->push('login_type', 'authorize');
                $this->template->push('response_type', $responseType);
                $this->template->push('client_id', $clientId);
                $this->template->push('redirect_uri', $redirectURI);
                $this->template->push('title', 'Authorize');
                $this->template->file('authorize.php');
                $this->showJson = false;
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
        $loginCount = $_SESSION['loginCount'];
        $requestType = OAuth2::REQUEST_TYPE_POST;
        $userid = $_POST[OAuth2::HTTP_QUERY_PARAM_USER_ID];
        $userid = $userid ? $userid : false;
        $username = $_POST[OAuth2::HTTP_QUERY_PARAM_USERNAME];
        $username = $username ? $username : false;
        $password = $_POST[OAuth2::HTTP_QUERY_PARAM_PASSWORD];
        $password = $password ? $password : '';
        
        $sUser = OAuth2::getSessionUser($request, $response);
        $responseType = OAuth2::getResponseType($request, $response);
        $clientId = OAuth2::getClientId($request, $response);
        $clientType = $responseType == OAuth2::RESPONSE_TYPE_TOKEN ? OAuth2::CLIENT_TYPE_IMPLICIT : OAuth2::CLIENT_TYPE_WEB;
        $redirectURI = OAuth2::getRedirectURI($request, $response);
        $check = OAuth2::checkRequest($request, $response, $requestType);
        
        if($other) {
            // 清除seesion memcache user,清除cookie
            $this->removeSessionUser();
            // 跳转至认证页
            $this->template->redirect($this->name, array(
                    'client_id' => $clientId,
                    'response_type' => $responseType,
                    'redirect_uri' => $redirectURI
            ));
        } else if($register) {
            $this->template->redirect(App::get('urls.register', $this->name));
        } else if($check) {
            //检测客户端信息
            $client = $this->clientService->checkClient($clientId, $clientType, $redirectURI);
            if($client) {
                if($sUser) {
                    $user = $this->userService->get($sUser['id']);
                    if($responseType == OAuth2::RESPONSE_TYPE_TOKEN) {
                        //生成token
                        $this->genToken($user, $client);
                    } else {
                        //生成code
                        $this->genCode($user, $client);
                    }
                } else {
                    //打开检测 登录用户任务
                    $user = $this->userService->checkUser(md5($password), $userid, $username);
                    if($user) {
                        //更新SESSION
                        $this->updateSessionUser($user);
                        if($responseType == OAuth2::RESPONSE_TYPE_TOKEN) {
                            //生成token
                            $this->genToken($user, $client);
                        } else {
                            //生成code
                            $this->genCode($user, $client);
                        }
                    } else {
                        //重新登录，可做
                        $this->template->push('error', '用户名密码错误');
                        $this->template->push('login_type', 'authorize');
                        $this->template->push('response_type', $responseType);
                        $this->template->push('client_id', $clientId);
                        $this->template->push('redirect_uri', $redirectURI);
                        $this->template->push('title', 'Authorize');
                        $this->template->file('authorize.php');
                        $this->showJson = false;
                    }
                }
            } else {
                $this->errorResponse('invalid_client');
            }
        } else {
            $this->errorResponse('invalid_request');
        }
    }
    protected function genToken($user, $client) {
        $redirectURI = $client['redirectURI'];
        list($accessToken, $refreshToken) = $this->oauth2TokenService->gen($user, $client);
        $fragment = array();
        if($accessToken) {
            $fragment['userid'] = $accessToken['userid'];
            $fragment['token'] = $accessToken['token'];
            $fragment['expires'] = $accessToken['expires'];
        }
        if($refreshToken) {
            $fragment['refresh_token'] = $accessToken['token'];
            $fragment['refresh_expires'] = $accessToken['expires'];
        }
        $this->template->redirect($redirectURI . '#' . http_build_query($fragment));
    }
    protected function genCode($user, $client) {
        $redirectURI = $client['redirectURI'];
        $oauth2code = $this->oauth2CodeService->gen($user, $client);
        if(is_array($oauth2code)) {
            $oauth2code = $oauth2code['code'];
        }
        $this->template->redirect($redirectURI, array('code' => $oauth2code));
    } 
    public function onStop() {
        if($this->showJson) {
            parent::onStop();
        } else {
            $this->template->display();
        }
    }
}
?>
