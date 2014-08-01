<?php
namespace sso\test;

use lay\core\Model;
use lay\model\Expireable;
use lay\App;

class Test extends Model implements Expireable {
    //private $id = 0;
    //private $name = '';//全英文字母名称
    //private $value = '';
    public function __construct() {
    }
    /**
     * @return array
     */
    public function properties() {
        return array(
            'id' => 0,
            'name' => '',
            'value' => ''
        );
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
            'name' => self::PROPETYPE_STRING,
            'value' => self::PROPETYPE_STRING
        );
    }
    public function table() {
        return 'lay_test';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'name' => 'name',
            'value' => 'value'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function setLifetime($lifetime) {
        App::set('lifetime.test', abs(intval($lifetime)));
    }
    public function getLifetime() {
        return abs(intval(App::get('lifetime.test', 1200)));
    }
}
?>
