<?php
namespace sso\test;

use lay\core\Service;
use lay\core\Store;

class TestService extends Service {
    public function __construct() {
        parent::__construct(Store::getInstance('sso\test\TestRedis'));
    }
}
?>
