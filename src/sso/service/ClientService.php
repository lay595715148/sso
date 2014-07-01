<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\ClientMongo;
use sso\store\ClientMemcache;
use demo\store\DemoUserMongo;
use demo\store\DemoStore;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;
use lay\util\Collector;
use lay\store\MemcacheStore;
use demo\model\DemoUser;

class ClientService extends Service {
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
     * DemoStore
     * @var DemoStore
     */
    protected $mysql;
    /**
     * ClientMongo
     * @var ClientMongo
     */
    protected $store;
    public function __construct() {
        $this->memcache = Store::getInstance('sso\store\ClientMemcache');
        parent::__construct(Store::getInstance('sso\store\ClientMongo'));
    }
    public function update($query, $info) {
        $ret = $this->store->update($query, $info);
        return $ret ? true : false;
    }
    public function checkClient($clientId, $clientType = false, $redirectURI = false, $clientSecret = false) {
        $query = array();
        if($clientId !== false) {
            $query['clientId'] = $clientId;
        }
        if($clientType !== false) {
            $query['clientType'] = $clientType;
        }
        if($redirectURI !== false) {
            $query['redirectURI'] = $redirectURI;
        }
        if($clientSecret !== false) {
            $query['clientSecret'] = $clientSecret;
        }
        $ret = $this->store->select($query, array());
        if($ret) {
            return $this->store->toOne();
        } else {
            return false;
        }
    }
    public function get($id) {
        $ret = $this->memcache->get($id);
        if(empty($ret)) {
            $ret = $this->store->get($id);
            if($ret) {
                EventEmitter::on(App::E_STOP, array($this, 'createInMemcache'), EventEmitter::L_HIGH, array($ret));
            }
        }
        return $ret;
    }
    public function upd($id, array $info) {
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'updateInMemcache'), EventEmitter::L_HIGH, array($id));
        }
        return $ret;
    }
    public function updateInMemcache($app, $id) {
        $info = $this->store->get($id);
        $this->memcache->upd($id, $info);
    }
    public function createInMemcache($app, $info) {
        Logger::info($info);
        $this->memcache->add($info);
    }
    /**
     * 测试mongo
     */
    public function mongo() {
        $this->demoUserMongo = Store::getInstance('demo\store\DemoUserMongo');
        //$this->demoUserMongo->connect();
        $ret = $this->demoUserMongo->get(2009);
        Logger::debug($ret);
        $ret = $this->demoUserMongo->upd(2009, array('name' => 'demo'.rand(1, 100000)));
        Logger::debug($ret);
        $ret = $this->demoUserMongo->del(2008);
        Logger::debug($ret);
        //$ret = $this->demoUserMongo->add(array('name' => 'name'.rand(1, 10000), 'pass' => '060bade8c5f6306ee81c832bb469e067', 'nick' => 'lay'.rand(1, 1000)));
        //Logger::debug($ret);
        $ret = $this->demoUserMongo->select(array(), array('_id', 'name'), array(), array(5, 5));
        //$ret = $this->demoUserMongo->toObjectArray();
        Logger::debug($ret);
        $ret = $this->demoUserMongo->count(array('_id' => array('$gt' => 2013)));
        Logger::debug($ret);
        //$dsStore = Store::getInstance('DemoSettingStore');
        //Logger::debug($dsStore->count(array('k', 'att', 'like')));
        //$ret = $this->store->select(array(), array(), array('id'=>'desc'), array(0, 5));
        //Logger::debug($ret);
    }
    /**
     * 测试mysql
     */
    public function mysql() {
        $offset = 3;
        $num = 5;
        $this->mysql = Store::getInstance('demo\store\DemoStore');
        $ret = $this->mysql->select(array('type' => array(0, '>')), array(), array(), array($offset, $num));
        Logger::debug($ret);
        $total = $this->mysql->count(array('type' => array(0, '>')));
        Logger::debug($total);
        $list = Collector::lister($ret, $total, $offset, $num)->toArray();
        Logger::debug($list);
        return $list;
    }
    /**
     * 测试mecmache
     */
    public function memcache() {
        $this->demoUserMongo = Store::getInstance('demo\store\DemoUserMongo');
        $ret = $this->demoUserMongo->get(2014);
        $memcacheStore = new MemcacheStore(new DemoUser());
        $r = $memcacheStore->add($ret);
        $r = $memcacheStore->get(2014);
        Logger::debug($r, 'DEMO', true);
        $r = $memcacheStore->upd(2013, $ret);
        //$ret = $memcacheStore->del(2013);
        $ret = $memcacheStore->get(2013);
        Logger::debug($ret, 'DEMO', true);
    }
}
?>
