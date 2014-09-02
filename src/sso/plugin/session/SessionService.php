<?php
namespace sso\plugin\session;

use lay\core\Service;
use lay\core\Store;
use lay\core\EventEmitter;
use lay\App;
use lay\core\Action;
use lay\util\Logger;
use sso\plugin\session\SessionMongo;
use sso\plugin\session\SessionRedis;
use sso\plugin\session\SessionMemcache;

class SessionService extends Service {
    /**
     * SessionService
     * @return SessionService
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    /**
     * SessionMongo
     * @var SessionMongo
     */
    protected $mongo;
    /**
     * SessionRedis
     * @var SessionRedis
     */
    protected $store;
    /**
     * SessionMemcache
     * @var SessionMemcache
     */
    //protected $memcache;
    public function __construct() {
        $this->mongo = SessionMongo::getInstance();
        //$this->memcache = SessionMemcache::getInstance();
        parent::__construct(SessionRedis::getInstance());
    }
    /**
     * 在数据库中清除过期的session
     */
    public function clean() {
        return $this->mongo->remove(array(
            '$or' => array(
                array('expires' => array('$lt' => time())), 
                array('data' => '')
            )
        ));
        //return $this->mongo->remove();
    }
    public function add(array $info) {
        $ret = parent::add($info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'addInMongo'), EventEmitter::L_HIGH, array($info));
        }
        return $ret;
    }
    public function upd($id, array $info) {
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'updateInMongo'), EventEmitter::L_HIGH, array($id));
        }
        return $ret;
    }
    public function addInMongo($app, $info) {
        $this->mongo->add($info);
    }
    public function updateInMongo($app, $id) {
        $info = $this->store->get($id);
        $this->mongo->upd($id, $info);
    }
}
?>
