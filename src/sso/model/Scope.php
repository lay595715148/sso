<?php
namespace sso\model;

use lay\core\Model;
use lay\model\Expireable;
use lay\App;
use lay\model\Secondary;

class Scope extends Model implements Expireable, Secondary {
    //private $id = 0;
    //private $name = '';//全英文字母名称
    //private $basis = 0;
    //private $description = '';
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
