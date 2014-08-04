<?php
namespace sso\action\route;

use lay\action\TypicalStaticAction;
use sso\service\UserService;
use lay\util\Logger;
use lay\action\StaticAction;
use lay\App;
use lay\util\Util;

class Userinfo extends TypicalStaticAction {
    /**
     * UserService
     * @var UserService
     */
    protected $userService;
    protected $user;
    public function onCreate() {
        $this->userService = UserService::getInstance();
        $this->template->file('userinfo.php');
    }
    public function onGet() {
        $request = $this->request;
        $response = $this->response;
        
        $id = intval(App::$_Parameter['id']);
        $ret = $this->userService->get($id);
        if($ret) {
            $this->user = $ret;
            $this->template->push('user', $ret);
        }
    }
    public function dir() {
        return 'www/html/user';
    }
    public function can() {
        return empty($this->user) ? false : true;
    }
}
?>
