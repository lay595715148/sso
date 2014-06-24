<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\User;

class UserMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
