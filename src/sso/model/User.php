<?php
namespace sso\model;

use lay\core\Model;
use lay\model\Expireable;
use lay\App;

/**
 * 用户对象
 * @author Lay Li
 * @property int $id
 * @property string $name
 * @property string $pass
 * @property string $nick
 * @method void setId(int $id) 给id属性赋值
 * @method void setName(string $name) 给name属性赋值
 * @method void setPass(string $pass) 给pass属性赋值
 * @method void setNick(string $nick) 给nick属性赋值
 * @method int getId() 获取id属性值
 * @method string getName() 获取name属性值
 * @method string getPass() 获取pass属性值
 * @method string getNick() 获取nick属性值
 */
class User extends Model implements Expireable {
    public function __construct() {
        parent::__construct(array(
            'id' => 0,
            'name' => '',
            'pass' => '',
            'nick' => ''
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
            'name' => self::PROPETYPE_STRING,
            'pass' => self::PROPETYPE_STRING,
            'nick' => self::PROPETYPE_STRING
        );
    }
    public function table() {
        return 'lay_user';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'name' => 'name',
            'pass' => 'pass',
            'nick' => 'nick'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function setLifetime($lifetime) {
        App::set('lifetime.user', $lifetime);
    }
    public function getLifetime() {
        return App::get('lifetime.user', 0);
    }
}
?>
