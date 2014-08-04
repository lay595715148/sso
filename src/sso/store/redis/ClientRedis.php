<?php
namespace sso\store\redis;

use lay\store\RedisStore;
use sso\model\Client;

class ClientRedis extends RedisStore {
    /**
     * ClientRedis
     * @return ClientRedis
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
