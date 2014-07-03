<?php
namespace sso\action;

use lay\action\HTMLAction;
use lay\App;

class Agreement extends HTMLAction {
    public function onGet() {
        $this->template->file(App::$_RootPath . '/www/html/agreement.html');
    }
    public function onPost() {
        $this->onGet();
    }
}
?>
