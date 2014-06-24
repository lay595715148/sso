<?php
namespace sso\model;

use lay\core\Model;

class User extends Model {
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
            'data' => self::PROPETYPE_STRING,
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
}
?>
