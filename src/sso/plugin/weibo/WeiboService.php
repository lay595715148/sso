<?php
namespace sso\plugin\weibo;

use lay\core\Service;
use lay\core\Store;

class WeiboService extends Service {
    /**
     * WeiboMongo
     * @var WeiboMongo
     */
    protected $store;
    public function __construct() {
        parent::__construct(Store::getInstance('sso\plugin\weibo\WeiboMongo'));
    }
}
?>
