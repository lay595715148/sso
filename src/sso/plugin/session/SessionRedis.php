<?php
namespace sso\plugin\session;

use lay\store\RedisStore;

class SessionRedis extends RedisStore {
    public function __construct() {
        parent::__construct(new Session());
    }
}
?>
