<?php
namespace sso\action;

use lay\util\Logger;

class Verify extends UAction {
    public function onGet() {
        $_POST = $_REQUEST;
        $this->onPost();
    }
    public function onPost() {
        $request = $this->request;
        $response = $this->response;
        
        $verifyCode = rand(10000, 99999);
        $this->updateVerifyCode($verifyCode);
    }
    protected function genVerifyCodeImage($verifyCode) {
        
    }
}
?>
