<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\Scope;

class ScopeMemcache extends MemcacheStore {
    /**
     * ScopeMemcache
     * @return ScopeMemcache
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new Scope());
    }
}
?>
