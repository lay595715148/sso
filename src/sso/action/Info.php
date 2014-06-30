<?php
namespace sso\action;

use sso\service\OAuth2TokenService;
use sso\service\ClientService;
use sso\core\OAuth2;
use lay\util\Logger;

class Info extends UAction {
    /**
     * ClientService
     * 
     * @var ClientService
     */
    protected $clientService;
    /**
     * OAuth2TokenService
     * 
     * @var OAuth2TokenService
     */
    protected $oauth2TokenService;
    public function onCreate() {
        $this->clientService = $this->service('sso\service\ClientService');
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
        
        $token = $_REQUEST['token'];
        $userid = $_REQUEST['userid'];
        $oauth2token = $this->oauth2TokenService->get($token);
        if($oauth2token && $userid == $oauth2token['userid'] && $oauth2token['type'] == OAuth2::TOKEN_TYPE_ACCESS) {
            $user = $this->userService->get($oauth2token['userid']);
            if($user) {
                $params = $this->genInfo($oauth2token, $user);
                $this->template->push($params);
                $this->template->push('user', $user);
            } else {
                $this->errorResponse('invalid_user');
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
