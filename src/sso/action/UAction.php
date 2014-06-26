<?php
namespace sso\action;

use lay\App;
use lay\action\JSONAction;
use lay\util\Logger;
use sso\core\OAuth2;
use sso\service\ClientService;
use lay\util\Collector;
use lay\action\TypicalAction;
use sso\service\UserService;

class UAction extends TypicalAction {
    /**
     * UserService
     * @var UserService
     */
    protected $userService;
    public function onCreate() {
        $this->userService = $this->service('sso\service\UserService');
    }
    public function removeSessionUser() {
        
    }
    public function updateSessionUser($user) {
        
    }
}
?>
