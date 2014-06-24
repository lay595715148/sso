<?php
namespace sso\model;

use lay\core\Model;

class OAuth2Token extends Model {
    public function __construct() {
        parent::__construct(array(
            'id' => '',
            'userid' => 0,
            'clientId' => '',
            'type' => 0,
            'expires' => 0
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_STRING,
            'userid' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'type' => self::PROPETYPE_INTEGER,
            'expires' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_oauth2_token';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'userid' => 'userid',
            'clientId' => 'clientId',
            'type' => 'type',
            'expires' => 'expires'
        );
    }
    public function primary() {
        return '_id';
    }
}
?>
