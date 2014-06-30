<?php
namespace sso\model;

use lay\core\Model;
use lay\core\Expireable;

class Session extends Model implements Expireable {
    public function __construct() {
        parent::__construct(array(
            'id' => '',
            'data' => '',
            'expires' => 0
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_STRING,
            'data' => self::PROPETYPE_STRING,
            'expires' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_session';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'data' => 'data',
            'expires' => 'expires'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function getLifetime() {
        return App::get('lifetime.scope', 18400);
    }
    public function setLifetime($lifetime) {
        App::set('lifetime.scope', intval($lifetime));
    }
}
?>
