<?php
namespace sso\plugin\session;

use lay\core\AbstractPlugin;
use lay\App;
use lay\core\Store;
use sso\service\SessionService;
use lay\core\Service;
use lay\core\EventEmitter;
use sso\model\Session;

class SessionPlugin extends AbstractPlugin {
    /**
     * SessionService
     * @var SessionService
     */
    private $sessionService;
    private $session;
    public function initilize() {
        @session_start();
        $this->addHook(App::H_INIT, array($this, 'initSession'));
        EventEmitter::on(App::E_DESTROY, array($this, 'updateSession'));
    }
    public function initSession() {
        $id = session_id();
        $this->sessionService = Service::getInstance('sso\service\SessionService');
        $session = $this->sessionService->get($id);
        if($session) {
            $_SESSION = $session['data'];
        }
    }
    public function updateSession() {
        $id = session_id();
        $data = $_SESSION;
        $session = new Session();
        //$session->setLifetime(18400);
        /* $info = array(
            'id' => $id,
            'data' => $data,
            'expires' => 18400
        );
        if($this->session) {
            
        } else {
            
        } */
    }
}
?>
