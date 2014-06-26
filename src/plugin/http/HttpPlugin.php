<?php
namespace plugin\http;

use lay\core\AbstractPlugin;
use lay\core\Action;
use lay\App;

class HttpPlugin extends AbstractPlugin {
    public function initilize() {
        $this->addHook(Action::H_STOP, array($this, 'isFound'));
    }
    public function isFound($action) {
        if(!$action) {
            try {
                @header("HTTP/1.1 404 Not Found");
            } catch (Exception $e) {
                // has output
            }
            include App::$_RootPath . '/www/html/404.html';
        }
    }
}
?>
