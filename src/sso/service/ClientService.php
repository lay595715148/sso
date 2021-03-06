<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\mongo\ClientMongo;
use sso\store\memcache\ClientMemcache;
use demo\store\DemoUserMongo;
use demo\store\DemoStore;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;
use lay\util\Collector;
use lay\store\MemcacheStore;
use demo\model\DemoUser;
use sso\store\redis\ClientRedis;
use lay\core\Action;

class ClientService extends Service {
    /**
     * ClientService
     * @return ClientService
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    /**
     * DemoUserMongo
     * @var DemoUserMongo
     */
    protected $demoUserMongo;
    /**
     * ClientMemcache
     * @var ClientMemcache
     */
    protected $memcache;
    /**
     * ClientRedis
     * @var ClientRedis
     */
    protected $redis;
    /**
     * DemoStore
     * @var DemoStore
     */
    protected $mysql;
    /**
     * ClientMongo
     * @var ClientMongo
     */
    protected $store;
    protected function __construct() {
        $this->memcache = ClientMemcache::getInstance();
        $this->redis = ClientRedis::getInstance();
        parent::__construct(ClientMongo::getInstance());
    }
    public function update($query, $info) {
        $ret = $this->store->update($query, $info);
        return $ret ? true : false;
    }
    public function checkClient($clientId, $clientType = false, $redirectURI = false, $clientSecret = false) {
        /* 
        $query = array();
        $query['clientId'] = $clientId;
        if($clientType !== false) {
            $query['clientType'] = $clientType;
        }
        if($redirectURI !== false) {
            $query['redirectURI'] = $redirectURI;
        }
        if($clientSecret !== false) {
            $query['clientSecret'] = $clientSecret;
        }
        $ret = $this->store->select($query);
        if($ret) {
            return $this->store->toOne();
        } else {
            return false;
        } */
        
        $ret = $this->get($clientId);
        if(empty($ret)) {
            return false;
        } else if($clientType !== false && $clientType != $ret['clientType']) {
            return false;
        } else if($redirectURI !== false && $redirectURI != $ret['redirectURI']) {
            return false;
        } else if($clientSecret !== false && $clientSecret != $ret['clientSecret']) {
            return false;
        }
        return $ret;
    }
    public function get($id) {
        //$ret = $this->memcache->get($id);
        $ret = $this->redis->get($id);
        if(empty($ret)) {
            if(is_string($id) && !is_numeric($id)) {
                $ret = $this->store->select(array('clientId' => $id));
                $ret = $this->store->toOne();
            } else {
                $ret = $this->store->get($id);
            }
            if($ret) {
                EventEmitter::on(App::E_STOP, array($this, 'createInRedis'), EventEmitter::L_HIGH, array($ret));
                //EventEmitter::on(App::E_STOP, array($this, 'createInMemcache'), EventEmitter::L_HIGH, array($ret));
            }
        }
        return $ret;
    }
    public function upd($id, array $info) {
        //$ret = $this->redis->upd($id, $info);
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'updateInRedis'), EventEmitter::L_HIGH, array($id));
            //EventEmitter::on(Action::E_STOP, array($this, 'updateInMemcache'), EventEmitter::L_HIGH, array($id));
        }
        return $ret;
    }
    public function del($id) {
        $ret = parent::del($id);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'removeInRedis'), EventEmitter::L_HIGH, array($id));
            //EventEmitter::on(App::E_STOP, array($this, 'removeInMemcache'), EventEmitter::L_HIGH, array($id));
        }
        return $ret;
    }
    public function updateInMemcache($app, $id) {
        $info = $this->store->get($id);
        $this->memcache->upd($id, $info);
    }
    public function createInMemcache($app, $info) {
        $this->memcache->add($info);
    }
    public function removeInMemcache($app, $id) {
        $this->memcache->del($id);
    }
    public function updateInRedis($app, $id) {
        $info = $this->store->get($id);
        $this->redis->upd($id, $info);
    }
    public function createInRedis($app, $info) {
        $this->redis->add($info);
    }
    public function removeInRedis($app, $id) {
        $this->redis->del($id);
    }
}
?>
