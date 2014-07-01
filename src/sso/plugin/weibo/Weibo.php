<?php
namespace sso\plugin\weibo;

use lay\core\Model;

class Weibo extends Model {
    public function __construct() {
        parent::__construct(array(
            'id' => 0
        ));
    }
    public function rules() {
        return array(
            'id' => self::PROPETYPE_INTEGER
        );
    }
    public function table() {
        return 'lay_plugin_weibo';
    }
    public function schema() {
        return 'laysoft';
    }
    public function columns() {
        return array(
            'id' => '_id'
        );
    }
    public function primary() {
        return '_id';
    }
}
?>
