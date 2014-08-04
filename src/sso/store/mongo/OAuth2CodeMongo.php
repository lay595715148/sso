<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\OAuth2Code;

class OAuth2CodeMongo extends MongoStore {
    /**
     * OAuth2CodeMongo
     * @return OAuth2CodeMongo
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new OAuth2Code());
    }
}
?>
