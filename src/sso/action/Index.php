<?php
namespace sso\action;

use lay\App;
use lay\action\HTMLAction;
use lay\util\Logger;

class Index extends HTMLAction {
    public function onGet() {
        //Logger::debug($argc);
    }
    public function onPost() {
        $this->onGet();
    }
}
?>
