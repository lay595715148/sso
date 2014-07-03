<?php
namespace sso\action\route;

use lay\action\HTMLAction;
use sso\service\UserService;
use lay\util\Logger;
use lay\action\StaticAction;
use lay\App;

class Userinfo extends StaticAction {
    /**
     * UserService
     * @var UserService
     */
    protected $userService;
    protected $user;
    protected $found;
    public function onCreate() {
        $this->userService = $this->service('sso\service\UserService');
        $this->template->file('userinfo.php');
    }
    public function onGet() {
        global $_PARAM;
        $request = $this->request;
        $response = $this->response;
        
        $id = intval($_PARAM['id']);
        $this->user = $this->userService->get($id);
        $this->template->push('user', $this->user);
    }
    
    public function find() {
        global $_PARAM;
        if(empty($this->found)) {
            $id = intval($_PARAM['id']);
            $template = $this->template->getFile();
            $filename = realpath(App::$_RootPath . '/www/html/user/' . $id . '.html');
            if(is_file($filename) && is_file($template)) {
                $origin = filemtime($template);
                $static = filemtime($filename);
                if($static > $origin) {
                    $this->found = $filename;
                } else {
                    $this->found = false;
                }
            } else {
                $this->found = false;
            }
        }
        return $this->found;
    }
    public function make() {
        global $_PARAM;
        if ($this->user) {
            $id = intval($_PARAM['id']);
            $dir = App::$_RootPath . '/www/html/user/';
            $filename = $dir . $id . '.html';
            $content = $this->template->out();
            $mkdir = is_dir($dir) ? true : mkdir($dir);
            $handle = fopen($filename, 'w');
            $result = fwrite($handle, $content);
            $return = fflush($handle);
            $return = fclose($handle);
        } else {
            $result = false;
        }
        return $result;
    }
}
?>