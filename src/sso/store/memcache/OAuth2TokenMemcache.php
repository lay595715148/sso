<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\OAuth2Token;

class OAuth2TokenMemcache extends MemcacheStore {
    /**
     * OAuth2TokenMemcache
     * @return OAuth2TokenMemcache
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new OAuth2Token());
    }
}
?>
