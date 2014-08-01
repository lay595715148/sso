<?php
namespace sso\model;

use lay\core\Model;
use lay\model\Expireable;
use lay\App;
use lay\model\Increment;
use lay\model\Secondary;

/**
 * 客户端对象
 * @author Lay Li
 * @ property int $id
 * @ property string $clientId
 * @ property string $clientName
 * @ property string $clientSecret
 * @ property int $clientType
 * @ property string $redirectURI
 * @ property string $scope
 * @ property string $location
 * @ property string $description
 * @ property string $icon
 * @method void setId(int $id) 给id属性赋值
 * @method void setClientId(string $clientId) 给clientId属性赋值
 * @method void setClientName(string $clientName) 给clientName属性赋值
 * @method void setClientSecret(string $clientSecret) 给clientSecret属性赋值
 * @method void setClientType(int $clientType) 给clientType属性赋值
 * @method void setRedirectURI(string $redirectURI) 给redirectURI属性赋值
 * @method void setScope(string $scope) 给scope属性赋值
 * @method void setLocation(string $location) 给location属性赋值
 * @method void setDescription(string $description) 给description属性赋值
 * @method void setIcon(string $icon) 给icon属性赋值
 * @method int getId() 获取id属性值
 * @method string getClientId() 获取clientId属性值
 * @method string getClientName() 获取clientName属性值
 * @method string getClientSecret() 获取clientSecret属性值
 * @method int getClientType() 获取clientType属性值
 * @method string getRedirectURI() 获取redirectURI属性值
 * @method string getScope() 获取scope属性值
 * @method string getLocation() 获取location属性值
 * @method string getDescription() 获取description属性值
 * @method string getIcon() 获取icon属性值
 */
class Client extends Model implements Expireable, Increment, Secondary {
    //private $id = 0;
    //private $clientId = '';
    //private $clientName = '';
    //private $clientSecret = '';
    //private $clientType = 0;
    //private $redirectURI = '';
    //private $scope = '';
    //private $location = '';
    //private $description = '';
    //private $icon = '';
    public function __construct() {
    }
    /**
     * @return array
     */
    public function properties() {
        return array(
            'id' => 0,
            'clientId' => '',
            'clientName' => '',
            'clientSecret' => '',
            'clientType' => 0,
            'redirectURI' => '',
            'scope' => '',
            'location' => '',
            'description' => '',
            'icon' => ''
        );
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'clientName' => self::PROPETYPE_STRING,
            'clientSecret' => self::PROPETYPE_STRING,
            'clientType' => self::PROPETYPE_INTEGER,
            'redirectURI' => self::PROPETYPE_STRING,
            'scope' => self::PROPETYPE_STRING,
            'location' => self::PROPETYPE_STRING,
            'description' => self::PROPETYPE_STRING,
            'icon' => self::PROPETYPE_STRING
        );
    }
    public function table() {
        return 'lay_client';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'clientId' => 'clientId',
            'clientName' => 'clientName',
            'clientSecret' => 'clientSecret',
            'clientType' => 'clientType',
            'redirectURI' => 'redirectURI',
            'scope' => 'scope',
            'location' => 'location',
            'description' => 'description',
            'icon' => 'icon'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function second() {
        return 'clientId';
    }
    
    public function sequence() {
        return '_id';
    }
    
    public function getLifetime() {
        return abs(intval(App::get('lifetime.client', 0)));
    }
    public function setLifetime($lifetime) {
        App::set('lifetime.client', abs(intval($lifetime)));
    }
}
?>
