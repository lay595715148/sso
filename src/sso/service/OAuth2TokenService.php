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
    public function gen($user, $client) {
        $tokens = array();
        $useRefresh = App::get('oauth2.use_refresh_token', true);
        $accessLifetime = App::get('oauth2.lifetime.token', 1800);
        $refreshLifetime = App::get('oauth2.lifetime.refresh_token', 18400);
        $accessToken = OAuth2::generateCode();
        $refreshToken = OAuth2::generateCode();
        
        $oauth2token = new OAuth2Token();
        $oauth2token->setToken($token);
        $oauth2token->setLifetime($lifetime);
        $oauth2token->setUserid($user['userid']);
        $oauth2token->setClientId($client['clientId']);
        $oauth2token->setType(OAuth2::TOKEN_TYPE_ACCESS);
        $tokens[] = $this->add($oauth2token->toArray());
        if($useRefresh) {
            $token = new OAuth2Token();
            $token->setToken($token);
            $token->setLifetime($lifetime);
            $token->setUserid($user['userid']);
            $token->setClientId($client['clientId']);
            $token->setType(OAuth2::TOKEN_TYPE_REFRESH);
            $tokens[] = $this->add($token->toArray());
        }
        return $tokens;
    }
}
?>
