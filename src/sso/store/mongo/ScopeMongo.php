<?php
namespace sso\store\mongo;

use lay\store\MongoStore;
use sso\model\Scope;

class ScopeMongo extends MongoStore {
    /**
     * ScopeMongo
     * @return ScopeMongo
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new Scope());
    }
}
?>
