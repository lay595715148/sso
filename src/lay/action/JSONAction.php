<?php
/**
 * 输出JSON格式
 * @abstract
 * @author Lay Li
 */
namespace lay\action;

use lay\core\Action;
use lay\entity\Response;
use lay\entity\Lister;
use lay\util\Util;
use lay\util\Logger;
use lay\util\Collector;

if(!defined('INIT_LAY')) {
    exit();
}

/**
 * 输出JSON格式
 * @abstract
 * @author Lay Li
 */
abstract class JSONAction extends Action {
    /**
     * (non-PHPdoc)
     * @see \lay\core\Action::onStop()
     */
    public function onStop() {
        $this->template->json();
        parent::onStop();
    }
}
?>
