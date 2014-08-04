<?php
namespace sso\store\mongo;

use lay\store\MongoStore;
use sso\model\Client;

class ClientMongo extends MongoStore {
    /**
     * ClientMongo
     * @return ClientMongo
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
