<?php
namespace sso\model;

use lay\core\Model;

class Session extends Model {
    public function __construct() {
        parent::__construct(array(
            'id' => '',
            'data' => '',
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_STRING,
            'data' => self::PROPETYPE_STRING
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
            'data' => 'data'
        );
    }
    public function primary() {
        return '_id';
    }
    protected function otherFormat($value, $propertype) {
        return $value;
    }
}
?>
