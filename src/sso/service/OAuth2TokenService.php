<?php
namespace sso\service;

use lay\core\Service;
use lay\core\Store;
use sso\store\OAuth2TokenMemcache;
use sso\store\OAuth2TokenMongo;
use lay\core\EventEmitter;
use lay\App;
use lay\core\Action;
use lay\util\Logger;
use sso\core\OAuth2;
use sso\model\OAuth2Token;

class OAuth2TokenService extends Service {
    /**
     * OAuth2CodeMongo
     * @var OAuth2TokenMongo
     */
    protected $mongo;
    /**
     * OAuth2CodeMemcache
     * @var OAuth2TokenMemcache
     */
    protected $store;
    public function __construct() {
        $this->mongo = Store::getInstance('sso\store\OAuth2TokenMongo');
        parent::__construct(Store::getInstance('sso\store\OAuth2TokenMemcache'));
    }
    /**
     * 在数据库中清除过期的令牌码数据
     */
    public function clean() {
        return $this->mongo->remove(array('expires' => array('$lt' => time())));
    }
    public function expire() {
        return $this->mongo->remove(array('expires' => array('$gt' => time())));
    }
    public function add(array $info) {
        $ret = $this->store->add($info);
        if($ret) {
            EventEmitter::on(Action::E_STOP, array($this, 'addInMongo'), EventEmitter::L_HIGH, array($info));
        }
        return $ret;
    }
    public function addInMongo($app, $info) {
        $this->mongo->add($info);
    }
    public function gen($user, $client, $scope = '') {
        $lifetime = App::get('oauth2.lifetime.token', 1800);
        $accessToken = OAuth2::generateCode();
        
        $oauth2token = new OAuth2Token();
        $oauth2token->setToken($accessToken);
        $oauth2token->setLifetime($lifetime);
        $oauth2token->setUserid($user['id']);
        $oauth2token->setClientId($client['clientId']);
        $oauth2token->setType(OAuth2::TOKEN_TYPE_ACCESS);
        $oauth2token->setScope($scope);
        $info = $oauth2token->toArray();
        $ret = $this->add($info);
        if($ret) {
            return $info;
        } else {
            return false;
        }
    }
    public function genRefresh($user, $client, $scope = '') {
        $lifetime = App::get('oauth2.lifetime.refresh_token', 18400);
        $refreshToken = OAuth2::generateCode();
        $oauth2token = new OAuth2Token();
        $oauth2token->setToken($refreshToken);
        $oauth2token->setLifetime($lifetime);
        $oauth2token->setUserid($user['id']);
        $oauth2token->setClientId($client['clientId']);
        $oauth2token->setType(OAuth2::TOKEN_TYPE_REFRESH);
        $oauth2token->setScope($scope);
        $info = $oauth2token->toArray();
        $ret = $this->add($info);
        if($ret) {
            return $info;
        } else {
            return false;
        }
    }
}
?>
