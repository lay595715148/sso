<?php
namespace sso\action;

use lay\App;
use lay\action\HTMLAction;
use lay\util\Logger;
use sso\sdk\SsoAuth;

class Index extends HTMLAction {
    public function onGet() {
        //Logger::debug($argc);
        $clientId = 'lay49515';
        $clientSecret = '2b53761249254ce6b502f521e5cc0683';
        $redriectURI = 'http://sso.laysoft.cn/redirect';
        $sso = new SsoAuth($clientId, $clientSecret, $redriectURI);
        $url = $sso->getAuthorizeURL();
        $this->template->redirect($url);
    }
    public function onPost() {
        $this->onGet();
    }
}
?>
