<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use lay\core\EventEmitter;
use lay\App;
use lay\core\Action;
use lay\util\Logger;
use sso\store\SessionMongo;
use sso\store\SessionMemcache;

class SessionService extends Service {
    /**
     * SessionMongo
     * @var SessionMongo
     */
    protected $mongo;
    /**
     * SessionMemcache
     * @var SessionMemcache
     */
    protected $store;
    public function __construct() {
        $this->mongo = Store::getInstance('sso\store\SessionMongo');
        parent::__construct(Store::getInstance('sso\store\SessionMemcache'));
    }
    /**
     * 在数据库中清除过期的session
     */
    public function clean() {
        return $this->mongo->remove(array('expires' => array('$lt' => time())));
    }
    public function upd($id, array $info) {
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'updateInMongo'), 0, array($id));
        }
        return $ret;
    }
    public function updateInMongo($app, $id) {
        $info = $this->store->get($id);
        $this->mongo->upd($id, $info);
    }
}
?>
