<?php

namespace sso\action;

use lay\util\Logger;
use lay\App;

class Verify extends UAction {
    public function onGet() {
        $_POST = $_REQUEST;
        $this->onPost();
    }
    public function onPost() {
        $request = $this->request;
        $response = $this->response;
        
        $verifyCode = $this->genVerifyCode();
        // 将产生的$verifyCode存放
        $bool = $this->updateVerifyCode($verifyCode);
        $bool = $this->genVerifyCodeImage($verifyCode);
    }
    protected function genVerifyCode($num = 4) {
        // 字符集 去掉了 0 1 o l O
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
        $strlen = strlen($str);
        // 存放产生的验证码字符串
        $code = "";
        for($i = 0; $i < $num; $i++) {
            $code .= $str[mt_rand(0, $strlen - 1)];
        }
        return $code;
    }
    protected function genVerifyCodeImage($code, $w = 60, $h = 25) {
        // 图片
        $im = imagecreate($w, $h);
        // 边框色
        $gray = imagecolorallocate($im, 118, 151, 199);
        // 背景色
        $bgcolor = imagecolorallocate($im, 216, 233, 249);
        //$bgcolor = imagecolorallocate($im, 255, 255, 255);
        // 画背景 画一矩形并填充
        imagefilledrectangle($im, 0, 0, $w, $h, $bgcolor);
        // 字体大小
        $size = 16;
        // 字符在图片中的X坐标
        $marginX = rand(4, 10);
        $len = strlen($code);
        $fontfile = realpath(App::$_RootPath . '/inc/DejaVuSansMono.ttf');
        for($i = 0; $i < $len; $i++) {
            $angle = mt_rand(- 15, 15);
            $marginY = mt_rand(1, 6) + 15;
            // 字体色
            $textColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
            // imagestring($im, 5, $strx, $strpos, substr($code, $i, 1), $textColor);
            imagefttext($im, $size, $angle, $marginX, $marginY, $textColor, $fontfile, substr($code, $i, 1));
            $marginX += rand(8, 14);
        }

        //header("Content-type: image/png");
        ob_start();
        imagepng($im);
        imagedestroy($im);
        $results = ob_get_contents();
        ob_end_clean();

        // 消息头
        $this->response->setContentType("image/png");
        $this->response->setData($results);
        
        return $results;
    }
    public function onStop() {
        $this->response->send();
    }
}
?>
