<?php
namespace sso\store\mongo;

use lay\store\MongoStore;
use sso\model\User;

class UserMongo extends MongoStore {
    /**
     * UserMongo
     * @return UserMongo
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new User());
    }
}
?>
