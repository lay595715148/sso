<?php
namespace sso\model;

use lay\core\Model;
use lay\core\Expireable;
use lay\App;

class Scope extends Model implements Expireable {
    public function __construct() {
        parent::__construct(array(
            'id' => 0,
            'basis' => 0,
            'description' => ''
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
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
            'basis' => 'basis',
            'description' => 'description'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function getLifetime() {
        return App::get('lifetime.scope', 0);
    }
    public function setLifetime($lifetime) {
        App::set('lifetime.scope', intval($lifetime));
    }
}
?>
