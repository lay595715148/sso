<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\OAuth2CodeMongo;
use sso\store\OAuth2CodeMemcache;
use lay\core\EventEmitter;
use lay\App;
use lay\core\Action;
use lay\util\Logger;

class OAuth2CodeService extends Service {
    /**
     * OAuth2CodeMongo
     * @var OAuth2CodeMongo
     */
    protected $mongo;
    /**
     * OAuth2CodeMemcache
     * @var OAuth2CodeMemcache
     */
    protected $store;
    public function __construct() {
        //$this->mongo = Store::getInstance('sso\store\OAuth2CodeMongo');
        parent::__construct(Store::getInstance('sso\store\OAuth2CodeMemcache'));
    }
    public function add($info) {
        $ret = $this->store->add($info);
        if($ret) {
            $this->info = $info;
            EventEmitter::on(Action::E_STOP, array($this, 'addInMongo'));
        }
        return $ret;
    }
    public function addInMongo() {
        Logger::debug($this->info);
        Store::getInstance('sso\store\OAuth2CodeMongo')->add($this->info);
    }
}
?>
