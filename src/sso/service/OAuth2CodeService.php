<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\mongo\OAuth2CodeMongo;
use sso\store\memcache\OAuth2CodeMemcache;
use lay\core\EventEmitter;
use lay\App;
use lay\core\Action;
use lay\util\Logger;
use sso\model\OAuth2Code;
use sso\core\OAuth2;

class OAuth2CodeService extends Service {
    /**
     * OAuth2CodeService
     * @return OAuth2CodeService
     */
    public static function getInstance() {
        return parent::getInstance();
    }
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
    protected function __construct() {
        $this->mongo = OAuth2CodeMongo::getInstance();
        parent::__construct(OAuth2CodeMemcache::getInstance());
    }
    /**
     * 在数据库中清除过期的授权码数据
     */
    public function clean() {
        return $this->mongo->remove(array('expires' => array('$lt' => time())));
    }
    public function expire() {
        return $this->mongo->remove(array('expires' => array('$gt' => time())));
    }
    public function add(array $info) {
        $ret = parent::add($info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'addInMongo'), EventEmitter::L_HIGH, array($info));
        }
        return $ret;
    }
    public function addInMongo($app, $info) {
        $this->mongo->add($info);
    }
    
    public function gen($user, $client, $scope = '') {
        $lifetime = App::get('oauth2.lifetime.code', 100);
        $code = OAuth2::generateCode();
        
        $oauth2code = new OAuth2Code();
        $oauth2code->setCode($code);
        $oauth2code->setLifetime($lifetime);
        $oauth2code->setUserid($user['id']);
        $oauth2code->setClientId($client['clientId']);
        $oauth2code->setScope($scope);
        //$oauth2code->setRedirectURI($client['redirectURI']);
        $ret = $this->add($oauth2code->toArray());
        if($ret) {
            return $code;
        } else {
            return false;
        }
    }
}
?>
