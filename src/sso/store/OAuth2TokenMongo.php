<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\OAuth2Token;

class OAuth2TokenMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new OAuth2Token());
    }
}
?>
