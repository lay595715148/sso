<?php
namespace sso\plugin\session;

use lay\core\AbstractPlugin;
use lay\App;
use lay\core\Store;
use lay\core\Service;
use lay\core\EventEmitter;
use lay\util\Logger;
use lay\core\Action;
use sso\plugin\session\SessionService;

class SessionPlugin extends AbstractPlugin {
    /**
     * SessionService
     * @var SessionService
     */
    private $sessionService;
    private $sessionId = false;
    public function initilize() {
        session_start();
        $_SESSION = array();
        $this->addHook(App::H_INIT, array($this, 'initSession'));
        //$this->addHook(Action::H_STOP, array($this, 'updateSession'));
        EventEmitter::on(App::E_DESTROY, array($this, 'updateSession'), EventEmitter::L_HIGH);
        //$this->addHook(Action::H_STOP, array($this, 'cleanSession'));
        $this->sessionService = SessionService::getInstance();
        //$this->sessionService->clean();
    }
    public function initSession() {
        $sessionId = session_id();
        $session = $this->sessionService->get($sessionId);
        if($session) {
            $this->sessionId = $sessionId;
            session_decode($session['data']);
        }
    }
    public function updateSession() {
        $id = session_id();
        $data = session_encode();
        if($data) {
            $session = new Session();
            $session->setId($id);
            $session->setData($data);
            $session->setLifetime(App::get('lifetime.session', 2400));
            if($this->sessionId && $this->sessionId == $id) {
                $this->sessionService->upd($id, $session->toArray());
            } else if($this->sessionId && $this->sessionId != $id) {
                $this->sessionService->del($this->sessionId);
                $this->sessionService->upd($id, $session->toArray());
            } else {
                $this->sessionService->add($session->toArray());
            }
        } else {
            $this->sessionService->del($id);
            if($this->sessionId != $id) {
                $this->sessionService->del($this->sessionId);
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
