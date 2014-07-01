<?php
namespace sso\plugin\session;

use lay\store\MongoStore;

class SessionMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new Session());
    }
}
?>
