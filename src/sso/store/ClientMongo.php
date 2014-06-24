<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\Client;

class ClientMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new Client());
    }
}
?>
