<?php
namespace sso\store\mongo;

use lay\store\MongoStore;
use sso\model\OAuth2Token;

class OAuth2TokenMongo extends MongoStore {
    /**
     * OAuth2TokenMongo
     * @return OAuth2TokenMongo
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    public function __construct() {
        parent::__construct(new OAuth2Token());
    }
}
?>
