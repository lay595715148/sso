<?php
namespace sso\store;

use lay\store\MemcacheStore;
use sso\model\OAuth2Token;

class OAuth2TokenMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new OAuth2Token());
    }
}
?>
