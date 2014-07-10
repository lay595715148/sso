<?php
namespace sso\action;

use sso\service\OAuth2TokenService;
use sso\service\ClientService;
use sso\core\OAuth2;
use lay\util\Logger;
use sso\service\ScopeService;

class Info extends UAction {
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
     * OAuth2TokenService
     * 
     * @var OAuth2TokenService
     */
    protected $oauth2TokenService;
    public function onCreate() {
        $this->clientService = ClientService::getInstance();
        $this->scopeService = ScopeService::getInstance();
        $this->oauth2TokenService = OAuth2TokenService::getInstance();
        parent::onCreate();
    }
    public function onGet() {
        $_POST = $_REQUEST;
        $this->onPost();
    }
    public function onPost() {
        $request = $this->request;
        $response = $this->response;
        
        $token = $_REQUEST['token'];
        $userid = $_REQUEST['userid'];
        $oauth2token = $this->oauth2TokenService->get($token);
        if($oauth2token && $oauth2token['type'] == OAuth2::TOKEN_TYPE_ACCESS) {// && $userid == $oauth2token['userid']
            list($scopeStr, $scopeArr) = $this->scopeService->filter($oauth2token['scope']);
            $splits = explode(',', $scopeStr);
            if(in_array('info', $splits) || in_array(1000, $splits)) {
                $user = $this->userService->get($oauth2token['userid']);
                if($user) {
                    $this->template->push('user', $user);
                    $params = $this->genInfo($oauth2token, $user);
                    $this->template->push($params);
                } else {
                    $this->errorResponse('dismissed_user');
                }
            } else {
                $this->errorResponse('unsupported_scope');
            }
        } else {
            $this->errorResponse('invalid_token');
        }
    }
    protected function genInfo($oauth2token, $user) {
        return array();
    }
}
?>
