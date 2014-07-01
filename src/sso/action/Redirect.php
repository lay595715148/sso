<?php
namespace sso\action;

use lay\App;
use lay\action\HTMLAction;
use lay\util\Logger;
use sso\service\OAuth2CodeService;
use sso\service\ClientService;
use sso\service\OAuth2TokenService;
use lay\action\TypicalAction;
use sso\sdk\SsoAuth;
use sso\core\OAuth2;

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
        parent::onCreate();
        $this->clientService = $this->service('sso\service\ClientService');
        $this->oauth2CodeService = $this->service('sso\service\OAuth2CodeService');
        $this->oauth2TokenService = $this->service('sso\service\OAuth2TokenService');
    }
    public function onGet() {
        $clientId = 'lay49515';
        $clientSecret = '2b53761249254ce6b502f521e5cc0683';
        $redriectURI = 'http://sso.laysoft.cn/redirect';
        $type = '';
        $options = array();
        $code = $_REQUEST['code'];
        $options['code'] = $code;
        $options['redirectURI'] = $redriectURI;
        
        $oauth2code = $this->oauth2CodeService->get($code);
        $this->template->push('code', $code);
        $this->template->push('oauth2code', $oauth2code);
        
        $sso = new SsoAuth($clientId, $clientSecret);
        $oauth2token = $sso->getToken($type, $options);
        $this->template->push('token', $oauth2token);
        $sso->accessToken = $oauth2token['content']['token'];
        $sso->refreshToken = $oauth2token['content']['refresh_token'];
        $ssoclient = $sso->toSsoClient();
        $userinfo = $ssoclient->getUserInfo();
        $this->template->push('userinfo', $userinfo);
    }
    public function onPost() {
        $_GET = $_REQUEST;
        $this->onGet();
    }
}
?>
