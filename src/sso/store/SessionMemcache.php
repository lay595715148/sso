<?php
namespace sso\store;

use lay\store\MemcacheStore;
use sso\model\Session;

class SessionMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new Session());
    }
}
?>
