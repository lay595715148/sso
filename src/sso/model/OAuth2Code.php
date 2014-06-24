<?php
namespace sso\model;

use lay\core\Model;

class OAuth2Code extends Model {
    public function __construct() {
        parent::__construct(array(
            'id' => '',
            'userid' => 0,
            'clientId' => '',
            'redirectURI' => '',
            'expires' => 0
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_STRING,
            'userid' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'redirectURI' => self::PROPETYPE_STRING,
            'expires' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_oauth2_code';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'userid' => 'userid',
            'clientId' => 'clientId',
            'redirectURI' => 'redirectURI',
            'expires' => 'expires'
        );
    }
    public function primary() {
        return '_id';
    }
}
?>
