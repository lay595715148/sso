<?php
namespace sso\store;

use lay\store\MemcacheStore;
use sso\model\Client;

class ClientMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
