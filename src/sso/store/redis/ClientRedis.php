<?php
namespace sso\store\redis;

use lay\store\RedisStore;
use sso\model\Client;

class ClientRedis extends RedisStore {
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
