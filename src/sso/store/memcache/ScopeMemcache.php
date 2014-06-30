<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\Scope;

class ScopeMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new Scope());
    }
}
?>
