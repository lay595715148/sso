<?php
namespace sso\store;

use lay\store\MemcacheStore;
use sso\model\User;

class UserMemcache extends MemcacheStore {
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
