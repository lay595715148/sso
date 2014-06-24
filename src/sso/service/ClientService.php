<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\ClientMongo;
use sso\store\ClientMemcache;

class ClientService extends Service {
    public function __construct() {
        parent::__construct(Store::getInstance('sso\store\ClientMongo'));
    }
    public function checkClient($clientId, $clientType, $redirectURI) {
        
    }
}
?>
