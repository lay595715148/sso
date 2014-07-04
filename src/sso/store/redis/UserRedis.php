<?php
namespace sso\store\redis;

use sso\model\User;
use lay\store\RedisStore;

class UserRedis extends RedisStore {
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
