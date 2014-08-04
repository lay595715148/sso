<?php
namespace sso\store\memcache;

use lay\store\MemcacheStore;
use sso\model\User;

class UserMemcache extends MemcacheStore {
    /**
     * UserMemcache
     * @return UserMemcache
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
