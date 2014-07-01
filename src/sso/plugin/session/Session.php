<?php
namespace sso\plugin\session;

use lay\core\Model;
use lay\core\Expireable;

/**
 * Session数据模型对象
 * @author Lay Li
 * @property string $id
 * @property string $data
 * @property int $expires
 * @method void setId(string $id) 给token属性赋值
 * @method void setData(string $data) 给data属性赋值
 * @method void setExpires(int $expires) 给expires属性赋值
 * @method string getId() 获取id属性值
 * @method string getData() 获取data属性值
 * @method int getExpires() 获取expires属性值
 */
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
        return $this->getExpires() - time();
    }
    public function setLifetime($lifetime) {
        $this->setExpires(time() + intval($lifetime));
    }
}
?>
