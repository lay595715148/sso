<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\UserMongo;
use sso\store\UserMemcache;
use lay\util\Logger;
use lay\core\EventEmitter;
use lay\App;

class UserService extends Service {
    /**
     * UserMemcache
     * @var UserMemcache
     */
    protected $memcache;
    /**
     * UserMongo
     * @var UserMongo
     */
    protected $store;
    public function __construct() {
        $this->memcache = Store::getInstance('sso\store\UserMemcache');
        parent::__construct(Store::getInstance('sso\store\UserMongo'));
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
        //去除password字段
        if($ret) {
            unset($ret['pass']);
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
        Logger::info($info);
        $this->memcache->add($info);
    }
    
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
