<?php

/**
 * 工具类
 *
 * @author Lay Li
 */
namespace lay\util;

if(! defined('INIT_LAY')) {
    exit();
}

/**
 * 工具类
 *
 * @author Lay Li
 */
class Util {
    /**
     * 服务器系统是不是Windows
     *
     * @var boolean
     */
    private static $_IsWindows = false;
    /**
     * 判断服务器系统是不是Windows
     *
     * @return boolean
     */
    public static function isWindows() {
        if(! is_bool(self::$_IsWindows)) {
            self::$_IsWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        }
        return self::$_IsWindows;
    }
    /**
     * 判断是不是绝对路径
     *
     * @param string $path            
     * @return boolean
     */
    public static function isAbsolutePath($path) {
        return false;
    }
    /**
     * 数组化
     * 
     * @param mixed $arr            
     * @return array
     */
    public static function arraylize($arr) {
        if(empty($arr)) {
            return array();
        } else if(is_array($arr)) {
            return (array)$arr;
        } else {
            return array(
                    $arr
            );
        }
    }
    /**
     * 可迭代化
     * 
     * @param mixed $arr            
     * @return Traversable
     */
    public static function iteratorize($arr) {
        return $arr instanceof \Traversable ? $arr : new \ArrayObject(self::arraylize($arr));
    }
    /**
     * 转变为纯粹的数组
     *
     * @param array $arr            
     * @return array
     */
    public static function toPureArray($arr) {
        $tmp = array();
        foreach(self::iteratorize($arr) as $item) {
            $tmp[] = $item;
        }
        return $tmp;
    }
    /**
     * 判断是不是纯粹的数组
     *
     * @param array $arr            
     * @return boolean
     */
    public static function isPureArray($arr) {
        $bool = true;
        if(is_array($arr)) {
            foreach($arr as $i => $a) {
                if(is_string($i) || ! is_int($i)) {
                    $bool = false;
                    break;
                }
            }
        } else {
            $bool = false;
        }
        return $bool;
    }
    
    /**
     * Is IP address in CIDR block?
     *
     * @return bool
     */
    public static function ipMatch($ip, $mask) {
        list($mask, $size) = explode('/', $mask . '/');
        $ipv4 = strpos($ip, '.');
        $max = $ipv4 ? 32 : 128;
        if(($ipv4 xor strpos($mask, '.')) || $size < 0 || $size > $max) {
            return FALSE;
        } elseif($ipv4) {
            $arr = array(
                    ip2long($ip),
                    ip2long($mask)
            );
        } else {
            $arr = unpack('N*', inet_pton($ip) . inet_pton($mask));
            $size = $size === '' ? 0 : $max - $size;
        }
        $bits = implode('', array_map(function ($n) {
            return sprintf('%032b', $n);
        }, $arr));
        return substr($bits, 0, $max - $size) === substr($bits, $max, $max - $size);
    }
    /**
     * 计算有没有下页
     *
     * @param int $total            
     * @param int $offset            
     * @param int $num            
     * @return boolean
     */
    public static function hasNext($total, $offset = -1, $num = -1) {
        if($offset == - 1 || $num == - 1) {
            return false;
        } else if($total > $offset + $num) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取带微秒数的当前时间
     *
     * @param string $format            
     * @return string number
     */
    public static function microtime($format = false) {
        if($format) {
            return date($format) . substr((string)microtime(), 1, 8);
        } else {
            return time() + substr((string)microtime(), 1, 8);
        }
    }
    /**
     * php array to php content
     *
     * @param array $arr
     *            convert array
     * @param boolean $encrypt
     *            if encrypt
     * @return string
     */
    public static function array2PHPContent($arr, $encrypt = false) {
        if($encrypt) {
            $r = '';
            $r .= self::array2String($arr);
        } else {
            $r = "<?php";
            $r .= "\r\nreturn ";
            self::a2s($r, $arr);
            $r .= ";\r\n?>\r\n";
        }
        return $r;
    }
    /**
     * convert a multidimensional array to url save and encoded string
     *
     * 在Array和String类型之间转换，转换为字符串的数组可以直接在URL上传递
     *
     * @param array $Array
     *            convert array
     * @return string
     */
    public static function array2String($Array) {
        $Return = '';
        $NullValue = "^^^";
        foreach($Array as $Key => $Value) {
            if(is_array($Value))
                $ReturnValue = '^^array^' . self::array2String($Value);
            else
                $ReturnValue = (strlen($Value) > 0) ? $Value : $NullValue;
            $Return .= urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)) . '||';
        }
        return urlencode(substr($Return, 0, - 2));
    }
    /**
     * convert a string generated with Array2String() back to the original (multidimensional) array
     *
     * @param string $String
     *            convert string
     * @return array
     */
    public static function string2Array($String) {
        $Return = array();
        $String = urldecode($String);
        $TempArray = explode('||', $String);
        $NullValue = urlencode(base64_encode("^^^"));
        foreach($TempArray as $TempValue) {
            list($Key, $Value) = explode('|', $TempValue);
            $DecodedKey = base64_decode(urldecode($Key));
            if($Value != $NullValue) {
                $ReturnValue = base64_decode(urldecode($Value));
                if(substr($ReturnValue, 0, 8) == '^^array^')
                    $ReturnValue = self::string2Array(substr($ReturnValue, 8));
                $Return[$DecodedKey] = $ReturnValue;
            } else {
                $Return[$DecodedKey] = null;
            }
        }
        return $Return;
    }
    /**
     * array $a to string $r
     *
     * @param string $r
     *            output string pointer address
     * @param array $a
     *            input array pointer address
     * @param array $l
     *            左则制表字符串
     * @param array $b
     *            左则制表字符串单元
     * @return void
     */
    public static function a2s(&$r, array &$a, $l = "", $b = "\t") {
        $f = false;
        $h = false;
        $i = 0;
        $r .= 'array(' . "\r\n";
        foreach($a as $k => $v) {
            if(! $h)
                $h = array(
                        'k' => $k,
                        'v' => $v
                );
            if($f)
                $r .= ',' . "\r\n";
            $j = ! is_string($k) && is_numeric($k) && $h['k'] === 0;
            self::o2s($r, $k, $v, $i, $j, $l, $b);
            $f = true;
            if($j && $k >= $i)
                $i = $k + 1;
        }
        $r .= "\r\n$l" . ')';
    }
    /**
     * to string $r
     *
     * @param string $r
     *            output string pointer address
     * @param string $k
     *            键名
     * @param string $v
     *            键值
     * @param string $i            
     * @param string $j            
     * @param array $l
     *            左则制表字符串
     * @param array $b
     *            左则制表字符串单元
     * @return void
     */
    private static function o2s(&$r, $k, $v, $i, $j, $l, $b) {
        $isW = self::isWindows();
        if($k !== $i) {
            if($j)
                $r .= "$l$b$k => ";
            else
                $r .= "$l$b'$k' => ";
        } else {
            $r .= "$l$b";
        }
        if(is_array($v))
            self::a2s($r, $v, $l . $b);
        else if(is_numeric($v))
            $r .= "" . $v;
        else
            $r .= "'" . str_replace("'", "\'", $v) . "'";
    }
    
    /**
     * xml format string to php array
     *
     * @param string $xml
     *            xml format string
     * @param bool $simple
     *            if use simplexml,default false
     * @return array bool
     */
    public static function xml2Array($xml, $simple = false) {
        if(! is_string($xml)) {
            return false;
        }
        if($simple) {
            $xml = @simplexml_load_string($xml);
        } else {
            $xml = @json_decode(json_encode((array)simplexml_load_string($xml)), 1);
        }
        return $xml;
    }
    /**
     * php array to xml format string
     *
     * @param array $value
     *            convert array
     * @param string $encoding
     *            xml encoding
     * @param string $root
     *            xml root tag
     * @param string $nkey
     *            纯数组转换时使用的标签名
     * @return string
     */
    public static function array2XML($value, $encoding = 'utf-8', $root = 'root', $nkey = '') {
        if(! is_array($value) && ! is_string($value) && ! is_bool($value) && ! is_numeric($value) && ! is_object($value)) {
            return false;
        }
        $nkey = preg_match('/^[A-Za-z_][A-Za-z0-9\-_]{0,}$/', $nkey) ? $nkey : '';
        return simplexml_load_string('<?xml version="1.0" encoding="' . $encoding . '"?>' . self::x2str($value, $root, $nkey))->asXml();
    }
    /**
     * object or array to xml format string
     *
     * @param object $xml
     *            array or object
     * @param string $key
     *            tag name
     * @param string $nkey
     *            纯数组转换时使用的标签名
     * @return string
     */
    private static function x2str($xml, $key, $nkey) {
        if(! is_array($xml) && ! is_object($xml)) {
            return "<$key>" . htmlspecialchars($xml) . "</$key>";
        }
        
        $xml_str = '';
        foreach($xml as $k => $v) {
            if(is_numeric($k)) {
                $k = $nkey ? $key . '-' . $nkey : $key . '-item';
            }
            $xml_str .= self::x2str($v, $k, $nkey);
        }
        return "<$key>$xml_str</$key>";
    }
    /**
     * 递归创建文件夹目录
     *
     * @param string $dir            
     * @return boolean
     */
    public static function createFolders($dir) {
        return is_dir($dir) | (self::createFolders(dirname($dir)) & mkdir($dir, 0777));
    }
    /**
     * Finds whether a variable is of expected type and do non-data-loss conversion.
     *
     * @param mixed $val            
     * @param string $type            
     * @return bool
     */
    public static function checkType(& $val, $type) {
        if(strpos($type, '|') !== false) {
            $found = null;
            foreach(explode('|', $type) as $type) {
                $tmp = $val;
                if(self::checkType($tmp, $type)) {
                    if($val === $tmp) {
                        return true;
                    }
                    $found[] = $tmp;
                }
            }
            if($found) {
                $val = $found[0];
                return true;
            }
            return false;
        } elseif(substr($type, - 2) === '[]') {
            if(! is_array($val)) {
                return false;
            }
            $type = substr($type, 0, - 2);
            $res = array();
            foreach($val as $k => $v) {
                if(! self::checkType($v, $type)) {
                    return false;
                }
                $res[$k] = $v;
            }
            $val = $res;
            return true;
        }
        
        switch(strtolower($type)) {
            case null:
            case 'mixed':
                return true;
            case 'bool':
            case 'boolean':
                return ($val === null || is_scalar($val)) && settype($val, 'bool');
            case 'string':
                return ($val === null || is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) && settype($val, 'string');
            case 'int':
            case 'integer':
                return ($val === null || is_bool($val) || is_numeric($val)) && ((float)(int)$val === (float)$val) && settype($val, 'int');
            case 'float':
                return ($val === null || is_bool($val) || is_numeric($val)) && settype($val, 'float');
            case 'scalar':
            case 'array':
            case 'object':
            case 'callable':
            case 'resource':
            case 'null':
                return call_user_func("is_$type", $val);
            default:
                return $val instanceof $type;
        }
    }
    /**
     * call object private method
     *
     * @param object $object            
     * @param string $method            
     * @param array $args            
     * @return mixed
     */
    public static function callPrivateMethod($object, $method, $args = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $closure = $reflection->getMethod($method)->getClosure($object);
        return call_user_func_array($closure, $args);
    }
    /**
     * get method closure.
     * If you want to use method closures and don't have PHP 5.3, perhaps you find useful the function
     *
     * @param object $object            
     * @param string $method            
     * @return string boolean
     */
    public static function getMethodClosure($object, $method) {
        if(method_exists(get_class($object), $method)) {
            $func = create_function('', ' 
                    $args             = func_get_args(); 
                    static $object    = NULL; 
                    
                    /* 
                    * Check if this function is being called to set the static $object, which 
                    * containts scope information to invoke the method 
                    */ 
                    if(is_null($object) && count($args) && get_class($args[0]) == "' . get_class($object) . '") { 
                        $object = $args[0]; 
                        return; 
                    } 

                    if(!is_null($object)) { 
                        return call_user_func_array(array($object, "' . $method . '"), $args); 
                    } else { 
                        return FALSE; 
                    }');
            
            // Initialize static $object
            $func($object);
            
            // Return closure
            return $func;
        } else {
            return false;
        }
    }
}
?>
