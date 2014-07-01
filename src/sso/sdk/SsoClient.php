<?php
namespace sso\sdk;

use lay\util\Logger;
class SsoClient extends SsoAuth {
    public $resourceURL = 'http://sso.laysoft.cn/info';
    public function getUserInfo() {
        $resourceURL = $this->resourceURL;
        $accessToken = $this->accessToken;
        $params = array('token' => $accessToken);
        $response = $this->http($resourceURL, 'GET', $params);
        //$response = $this->fopen($resourceURL,'GET',$params);
        $result = json_decode($response,true);
        return $result;
    }
}
?>
