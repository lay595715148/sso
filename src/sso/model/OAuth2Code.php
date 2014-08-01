<?php
namespace sso\model;

use lay\core\Model;
use lay\model\Expireable;

/**
 * OAuth2 Code对象
 * @author Lay Li
 * @ property string $code
 * @ property int $userid
 * @ property string $clientId
 * @ property string $redriectURI
 * @ property int $expires
 * @method void setCode(string $code) 给code属性赋值
 * @method void setUserid(int $userid) 给userid属性赋值
 * @method void setClientId(string $clientId) 给clientId属性赋值
 * @method void setScope(string $scope) 给scope属性赋值
 * @method void setExpires(int $expires) 给expires属性赋值
 * @method string getCode() 获取code属性值
 * @method int getUserid() 获取userid属性值
 * @method string getClientId() 获取clientId属性值
 * @method string getScope() 获取scope属性值
 * @method int getExpires() 获取expires属性值
 */
class OAuth2Code extends Model implements Expireable {
    //private $code = '';
    //private $userid = 0;
    //private $clientId = '';
    //private $scope = '';
    //private $expires = 0;
    public function __construct() {
    }
    /**
     * @return array
     */
    public function properties() {
        return array(
            'code' => '',
            'userid' => 0,
            'clientId' => '',
            'scope' => '',
            'expires' => 0
        );
    }
    public function rules() {
        return array(
            'code' => self::PROPETYPE_STRING,
            'userid' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'scope' => self::PROPETYPE_STRING,
            'expires' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_oauth2_code';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'code' => '_id',
            'userid' => 'userid',
            'clientId' => 'clientId',
            'scope' => 'scope',
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
        return abs($this->getExpires() - time());
    }
}
?>
