<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\Session;

class SessionMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new Session());
    }
}
?>
