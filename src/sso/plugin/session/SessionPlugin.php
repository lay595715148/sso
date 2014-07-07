<?php
namespace sso\plugin\session;

use lay\core\AbstractPlugin;
use lay\App;
use lay\core\Store;
use lay\core\Service;
use lay\core\EventEmitter;
use lay\util\Logger;
use lay\core\Action;

class SessionPlugin extends AbstractPlugin {
    /**
     * SessionService
     * @var SessionService
     */
    private $sessionService;
    private $sessionFlag = false;
    public function initilize() {
        session_start();
        $_SESSION = array();
        $this->addHook(App::H_INIT, array($this, 'initSession'));
        $this->addHook(Action::H_STOP, array($this, 'updateSession'));
        $this->addHook(App::H_STOP, array($this, 'cleanSession'));
        $this->sessionService = Service::getInstance('sso\plugin\session\SessionService');
        $this->sessionService->clean();
    }
    public function initSession() {
        $session = $this->sessionService->get(session_id());
        if($session) {
            $this->sessionFlag = true;
            session_decode($session['data']);
        } else {
            session_decode('');
        }
    }
    public function updateSession() {
        $id = session_id();
        $data = session_encode();
        if($data) {
            $session = new Session();
            $session->setId($id);
            $session->setData($data);
            $session->setLifetime(App::get('lifetime.scope', 2400));
            if($this->sessionFlag) {
                $this->sessionService->upd($id, $session->toArray());
            } else {
                $this->sessionService->add($session->toArray());
            }
        }
    }
    /**
     * 
     */
    public function cleanSession() {
        $this->sessionService->clean();
    }
}
?>
