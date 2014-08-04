<?php
namespace sso\model;

use lay\core\Model;
use lay\model\Expireable;
use lay\App;
use lay\model\Secondary;

/**
 * OAuth2 Scope对象
 * @author Lay Li
 * @method void setId(int $id) 给id属性赋值
 * @method void setName(string $name) 给name属性赋值
 * @method void setBasis(int $basis) 给basis属性赋值
 * @method void setDescription(string $description) 给description属性赋值
 * @method int getId() 获取id属性值
 * @method string getName() 获取name属性值
 * @method int getBasis() 获取basis属性值
 * @method string getDescription() 获取description属性值
 */
class Scope extends Model implements Expireable, Secondary {
    public function __construct() {
    }
    /**
     * @return array
     */
    public function properties() {
        return array(
            'id' => 0,
            'name' => '',
            'basis' => 0,
            'description' => ''
        );
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
            'name' => self::PROPETYPE_STRING,
            'basis' => self::PROPETYPE_INTEGER,
            'description' => self::PROPETYPE_STRING
        );
    }
    public function table() {
        return 'lay_scope';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'name' => 'name',
            'basis' => 'basis',
            'description' => 'description'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function second() {
        return 'name';
    }

    public function setLifetime($lifetime) {
        App::set('lifetime.scope', abs(intval($lifetime)));
    }
    public function getLifetime() {
        return abs(intval(App::get('lifetime.scope', 0)));
    }
}
?>
