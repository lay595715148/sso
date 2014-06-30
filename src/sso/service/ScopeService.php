<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;
use sso\store\mongo\ScopeMongo;
use sso\store\memcache\ScopeMemcache;

class ScopeService extends Service {
    /**
     * ScopeMemcache
     * @var ScopeMemcache
     */
    protected $memcache;
    /**
     * ScopeMongo
     * @var ScopeMongo
     */
    protected $store;
    public function __construct() {
        $this->memcache = Store::getInstance('sso\store\memcache\ScopeMemcache');
        parent::__construct(Store::getInstance('sso\store\mongo\ScopeMongo'));
    }
    public function update($query, $info) {
        //$ret = $this->store->update($query, $info);
        //同时更新缓存
        $rets = $this->store->select($query);
        if($rets) {
            foreach ($rets as $ret) {
                $this->upd($ret['id'], $info);
            }
        }
        return $rets ? true : false;
    }
    public function getList($ids) {
        $rets = array();
        foreach ((array)$ids as $id) {
            $rets[] = $this->get($id);
        }
        return $rets;
    }
    public function get($id) {
        $ret = $this->memcache->get($id);
        if(empty($ret)) {
            $ret = $this->store->get($id);
            //增加存入缓存任务
            if($ret) {
                EventEmitter::on(App::E_STOP, array($this, 'createInMemcache'), 0, array($ret));
            }
        }
        return $ret;
    }
    public function upd($id, array $info) {
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'updateInMemcache'), 0, array($id));
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
}
?>
