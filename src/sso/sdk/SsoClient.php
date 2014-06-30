<?php
namespace sso\sdk;

class SsoClient extends SsoSdk {
    public $resourceURL = 'http://localhost/longmeng/SSO/src/resource.php';
    public function getUserInfo() {
        $resourceURL = $this->resourceURL;
        $access_token = $this->access_token;
        $params = array('access_token' => $access_token);
        //$response = $this->http($resourceURL,'GET',$params);
        $response = $this->fopen($resourceURL,'GET',$params);
        $result = json_decode($response,true);
        return $result;
    }
}
?>
