<?php
namespace sso\action;

use lay\App;
use lay\action\HTMLAction;
use lay\util\Logger;
use sso\service\OAuth2CodeService;
use sso\service\ClientService;
use sso\service\OAuth2TokenService;
use lay\action\TypicalAction;

class Redirect extends TypicalAction {
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
        $code = $_REQUEST['code'];
        $token = $_REQUEST['token'];
        if($code) {
            $this->template->push('code', $code);
            $oauth2code = $this->oauth2CodeService->get($code);
            $this->template->push('oauth2code', $oauth2code);
        }
        if($token) {
            $this->template->push('token', $token);
            $oauth2token = $this->oauth2CodeService->get($token);
            $this->template->push('oauth2token', $oauth2token);
        }
    }
    public function onPost() {
        $this->onGet();
    }
}
?>
