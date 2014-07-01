<?php
namespace sso\plugin\session;

use lay\store\MemcacheStore;

class SessionMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new Session());
    }
}
?>
