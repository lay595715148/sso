<?php
namespace sso\action;

use lay\App;
use lay\action\JSONAction;
use lay\util\Logger;
use sso\core\OAuth2;
use sso\service\ClientService;

class Authorize extends JSONAction {
    private $showJson = true;
    /**
     * ClientService
     * @var ClientService
     */
    private $clientService;
    public function onRequest() {
        $this->clientService = $this->service('sso\service\ClientService');
    }
    public function onGet() {
        $ret = $this->clientService->get(50);
        //$ret = $this->clientService->get(50);
        $check = OAuth2::checkRequest($this->request, $this->response);
        Logger::debug($ret);
    }
    public function onPost() {
        
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
