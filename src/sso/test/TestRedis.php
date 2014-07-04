<?php
namespace sso\test;

use lay\store\RedisStore;

class TestRedis extends RedisStore {
    public function __construct() {
        parent::__construct(new Test());
    }
}
?>
