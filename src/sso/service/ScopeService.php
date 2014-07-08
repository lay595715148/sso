<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;
use sso\store\mongo\ScopeMongo;
use sso\store\memcache\ScopeMemcache;
use lay\store\MemcacheStore;
use lay\core\Action;

class ScopeService extends Service {
    /**
     * ScopeMemcache
     * @var ScopeMemcache
     */
    protected $memcache;
    /**
     * MemcacheStore
     * @var MemcacheStore
     */
    protected $memcacheStore;
    /**
     * ScopeMongo
     * @var ScopeMongo
     */
    protected $store;
    public function __construct() {
        $this->memcacheStore = Store::getInstance('lay\store\MemcacheStore');
        $this->memcache = Store::getInstance('sso\store\memcache\ScopeMemcache');
        parent::__construct(Store::getInstance('sso\store\mongo\ScopeMongo'));
    }
    /**
     * 过滤掉不合法的scope
     * @param string|array $scope
     * @return array
     */
    public function filter($scope) {
        $model = $this->memcache->getModel();
        $table = $model->table();
        $pk = $model->primary();
        $key = $table.'.'.$pk;
        $basisKey = $table.'.'.$pk.'.basis';
        if(empty($scope)) {
            $tmp = array();
            //获取缓存中的所有scope
            $scope = $this->memcacheStore->get($key);
            if(empty($scope)) {
                $ret = $this->cacheAll();
                $scope = $this->memcacheStore->get($key);
            }
            if($scope) {
                $scope = $this->getList($scope);
                foreach ($scope as $s) {
                    $tmp[$s['id']] = $s;
                }
            }
            $str = implode(',', array_keys($tmp));
            $arr = array_values($tmp);
            $scope = array($str, $arr);
        } else if(is_string($scope)) {
            $scope = trim($scope) ? array_map('trim', explode(',', $scope)) : '';
            $scope = $this->filter($scope);
        } else if(is_array($scope)) {
            //获取缓存中的basis scope
            $tmp = array();
            $scope = $this->getList($scope);
            $basis = $this->memcacheStore->get($basisKey);
            if(empty($basis)) {
                $ret = $this->cacheBasis();
                $basis = $this->memcacheStore->get($basisKey);
            }
            if($basis) {
                $basis = $this->getList($basis);
                foreach ($basis as $b) {
                    $tmp[$b['id']] = $b;
                }
            }
            if($scope) {
                foreach ($scope as $s) {
                    $tmp[$s['id']] = $s;
                }
            }
            ksort($tmp);
            $str = implode(',', array_keys($tmp));
            $arr = array_values($tmp);
            $scope = array($str, $arr);
        }
        return $scope;
    }
    public function cacheAll() {
        $model = $this->memcache->getModel();
        $table = $model->table();
        $pk = $model->primary();
        $id = $table.'.'.$pk;
        $all = $this->getAll();
        $ids = array();
        foreach ($all as $a) {
            $ids[] = $a['id'];
        }
        return $this->memcacheStore->set($id, $ids);
    }
    public function cacheBasis() {
        $model = $this->memcache->getModel();
        $table = $model->table();
        $pk = $model->primary();
        $id = $table.'.'.$pk.'.basis';
        $all = $this->getBasisList();
        $ids = array();
        foreach ($all as $a) {
            $ids[] = $a['id'];
        }
        return $this->memcacheStore->set($id, $ids);
    }
    public function getAll() {
        return $this->store->select(array(), array(), array('id' => 'ASC'));
    }
    public function getBasisList() {
        return $this->store->select(array('basis' => 1));
    }
    public function update($query, $info) {
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
    public function add(array $info) {
        $ret = parent::add($info);
        if($ret) {
            //增加存入缓存任务
            EventEmitter::on(App::E_STOP, array($this, 'cacheAll'), EventEmitter::L_HIGH);
            if($info['basis']) {
                EventEmitter::on(App::E_STOP, array($this, 'cacheBasis'), EventEmitter::L_HIGH);
            }
        }
    }
    //$id could be primary key or second key
    public function get($id) {
        $ret = $this->memcache->get($id);
        if(empty($ret)) {
            if(is_string($id) && !is_numeric($id)) {
                $ret = $this->store->select(array('name' => $id));
                $ret = $this->store->toOne();
            } else {
                $ret = $this->store->get($id);
            }
            //增加存入缓存任务
            if($ret) {
                EventEmitter::on(App::E_STOP, array($this, 'createInMemcache'), EventEmitter::L_HIGH, array($ret));
            }
        }
        return $ret;
    }
    //$id must be primary key
    public function upd($id, array $info) {
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'updateInMemcache'), EventEmitter::L_HIGH, array($id));
            EventEmitter::on(App::E_STOP, array($this, 'cacheAll'), EventEmitter::L_HIGH);
        }
        return $ret;
    }
    public function del($id) {
        $ret = parent::del($id);
        if($ret) {
            EventEmitter::on(App::E_STOP, array($this, 'removeInMemcache'), EventEmitter::L_HIGH, array($id));
            EventEmitter::on(App::E_STOP, array($this, 'cacheAll'), EventEmitter::L_HIGH);
            EventEmitter::on(App::E_STOP, array($this, 'cacheBasis'), EventEmitter::L_HIGH);
        }
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
}
?>
