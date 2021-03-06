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
        $this->userService = UserService::getInstance();
        parent::onCreate();
    }
    protected function removeSessionUser() {
        unset($_SESSION['userid']);
        unset($_SESSION['username']);
        unset($_SESSION['usernick']);
        session_regenerate_id(true);
    }
    /**
     * 
     * @param array $user
     */
    protected function updateSessionUser($user) {
        $_SESSION['userid'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['usernick'] = $user['nick'];
    }
    protected function checkVerifyCode($verifyCode) {
        if($_SESSION['verifyCode']) {
            if(strtolower($_SESSION['verifyCode']) == strtolower($verifyCode)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    protected function removeVerifyCode() {
        unset($_SESSION['verifyCode']);
        unset($_SESSION['loginCount']);
    }
    protected function updateLoginCount() {
        $loginCount = ++$_SESSION['loginCount'];
        return $loginCount;
    }
    protected function updateVerifyCode($verifyCode) {
            $_SESSION['verifyCode'] = $verifyCode;
    }
    protected function isNeedVerification() {
        $loginCount = $_SESSION['loginCount'];
        return $loginCount && $loginCount > 1 ? true : false;
    }
}
?>
