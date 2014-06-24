<?php
namespace sso\store;

use lay\store\MongoStore;
use sso\model\OAuth2Code;

class OAuth2CodeMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new OAuth2Code());
    }
}
?>
