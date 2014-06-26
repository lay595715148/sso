<?php
namespace sso\model;

use lay\core\Model;
use lay\core\Expireable;

/**
 * OAuth2 Code对象
 * @author Lay Li
 * @property string $token
 * @property int $userid
 * @property string $clientId
 * @property string $type
 * @property int $expires
 * @method void setToken(string $token) 给token属性赋值
 * @method void setUserid(int $userid) 给userid属性赋值
 * @method void setClientId(string $clientId) 给clientId属性赋值
 * @method void setType(int $type) 给type属性赋值
 * @method void setExpires(int $expires) 给expires属性赋值
 * @method string getToken() 获取token属性值
 * @method int getUserid() 获取userid属性值
 * @method string getClientId() 获取clientId属性值
 * @method int getType() 获取type属性值
 * @method int getExpires() 获取expires属性值
 */
class OAuth2Token extends Model implements Expireable {
    public function __construct() {
        parent::__construct(array(
            'token' => '',
            'userid' => 0,
            'clientId' => '',
            'type' => 0,
            'expires' => 0
        ));
    }
    public function rules() {
        return array(
            'token' => self::PROPETYPE_STRING,
            'userid' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'type' => self::PROPETYPE_INTEGER,
            'expires' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_oauth2_token';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'token' => '_id',
            'userid' => 'userid',
            'clientId' => 'clientId',
            'type' => 'type',
            'expires' => 'expires'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function setLifetime($lifetime) {
        $this->setExpires(time() + $lifetime);
    }
    public function getLifetime() {
        return $this->getExpires() - time();
    }
}
?>
