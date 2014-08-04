<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\OAuth2Code;

class OAuth2CodeMemcache extends MemcacheStore {
    /**
     * OAuth2CodeMemcache
     * @return OAuth2CodeMemcache
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new OAuth2Code());
    }
}
?>
