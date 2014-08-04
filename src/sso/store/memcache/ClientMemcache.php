<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\Client;

class ClientMemcache extends MemcacheStore {
    /**
     * ClientMemcache
     * @return ClientMemcache
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
