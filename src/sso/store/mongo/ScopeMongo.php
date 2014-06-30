<?php
namespace sso\store\mongo;

use lay\store\MongoStore;
use sso\model\Scope;

class ScopeMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new Scope());
    }
}
?>
