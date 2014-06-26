<?php
namespace sso\model;

use lay\core\Model;
use lay\core\Expireable;
use lay\App;

class Client extends Model implements Expireable {
    public function __construct() {
        parent::__construct(array(
            'id' => 0,
            'clientId' => '',
            'clientName' => '',
            'clientSecret' => '',
            'clientType' => 1,
            'redirectURI' => '',
            'location' => '',
            'description' => '',
            'icon' => ''
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER,
            'clientId' => self::PROPETYPE_STRING,
            'clientName' => self::PROPETYPE_STRING,
            'clientSecret' => self::PROPETYPE_STRING,
            'clientType' => self::PROPETYPE_INTEGER,
            'redirectURI' => self::PROPETYPE_STRING,
            'location' => self::PROPETYPE_STRING,
            'description' => self::PROPETYPE_STRING,
            'icon' => self::PROPETYPE_STRING
        );
    }
    public function table() {
        return 'lay_client';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id',
            'clientId' => 'clientId',
            'clientName' => 'clientName',
            'clientSecret' => 'clientSecret',
            'clientType' => 'clientType',
            'redirectURI' => 'redirectURI',
            'location' => 'location',
            'description' => 'description',
            'icon' => 'icon'
        );
    }
    public function primary() {
        return '_id';
    }
    
    public function getLifetime() {
        return App::get('lifetime.client', 1800);
    }
    public function setLifetime($lifetime) {
        App::set('lifetime.client', intval($lifetime));
    }
}
?>
