<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\UserMongo;
use sso\store\UserMemcache;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;
use sso\store\redis\UserRedis;

class UserService extends Service {
    /**
     * UserMemcache
     * @var UserMemcache
     */
    protected $memcache;
    /**
     * UserRedis
     * @var UserRedis
     */
    protected $redis;
    /**
     * UserMongo
     * @var UserMongo
     */
    protected $store;
    public function __construct() {
        $this->memcache = Store::getInstance('sso\store\UserMemcache');
        $this->redis = Store::getInstance('sso\store\redis\UserRedis');
        parent::__construct(Store::getInstance('sso\store\UserMongo'));
    }
    public function get($id) {
        //$ret = $this->memcache->get($id);
        $ret = $this->redis->get($id);
        if(empty($ret)) {
            $ret = $this->store->get($id);
            //增加存入缓存任务
            if($ret) {
                EventEmitter::on(App::E_STOP, array($this, 'createInRedis'), EventEmitter::L_HIGH, array($ret));
            }
        }
        //去除password字段
        if($ret) {
            unset($ret['pass']);
        }
        return $ret;
    }
    public function upd($id, array $info) {
        //$ret = $this->redis->upd($id, $info);
        $ret = parent::upd($id, $info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'updateInRedis'), EventEmitter::L_HIGH, array($id));
        }
        return $ret;
    }
    public function del($id) {
        $ret = $this->redis->del($id);
        return parent::del($id);
    }
    public function updateInMemcache($app, $id) {
        $info = $this->store->get($id);
        $this->memcache->upd($id, $info);
    }
    public function updateInRedis($app, $id) {
        $info = $this->store->get($id);
        $this->redis->upd($id, $info);
    }
    public function createInMemcache($app, $info) {
        $this->memcache->add($info);
    }
    public function createInRedis($app, $info) {
        $this->redis->add($info);
    }
    
    /**
     * 验证用户
     * @param string $password
     * @param string $userid
     * @param string $username
     * @return boolean|array
     */
    public function checkUser($password, $userid = false, $username = false) {
        if($userid === false && $username === false) {
            return false;
        } else {
            $query = array();
            $query['pass'] = $password;
            if($userid !== false) {
                $query['id'] = $userid;
            } else if($username !== false) {
                $query['name'] = $username;
            }
            $ret = $this->store->select($query, array());
            if($ret) {
                return $this->store->toOne();
            } else {
                return false;
            }
        }
    }
}
?>
