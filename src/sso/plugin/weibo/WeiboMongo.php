<?php
namespace sso\plugin\weibo;

use lay\store\MongoStore;

class WeiboMongo extends MongoStore {
    public function __construct() {
        parent::__construct(new Weibo());
    }
}
?>
