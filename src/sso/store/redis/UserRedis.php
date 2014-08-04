<?php
namespace sso\store\redis;

use sso\model\User;
use lay\store\RedisStore;

class UserRedis extends RedisStore {
    /**
     * UserRedis
     * @return UserRedis
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
