<?php
use lay\util\Logger;

return array(
    'logger' => array(Logger::L_ALL & ~Logger::L_INFO, false, 0),
    'appname' => 'sso',
    'theme' => 'default',
    'themes' => array(
        'default' => array(
            'dir' => realpath(__DIR__ . '/../../themes/default')
        )
    ),
    'plugins' => array(
        'http' => array(
            'name' => 'http',
            'classname' => 'plugin\http\HttpPlugin'
        ),
        'session' => array(
            'name' => 'session',
            'classname' => 'sso\plugin\session\SessionPlugin'
        )
    ),
    'filters' => array(
        'observe' => array(
            'priority' => 1,
            'classname' => 'sso\filter\ObserveFilter'
        ),
        'user_exists' => array(
            'priority' => 2,
            'classname' => 'sso\filter\UserExistsFilter'
        )
    ),
    'routers' => array(
        array(
            'rule' => '/^\/user-(?P<id>\d+)\.(?P<ext>html|htm)$/',
            //'host' => 'web.lay.laysoft.cn',//多个用|做分隔
            //'ip' => '127.0.0.1',//多个用|做分隔
            //'port' => 80,//多个用|做分隔
            'name' => 'userinfo',
            'classname' => 'sso\action\route\Userinfo'
        )
    ),
    'actions' => array(
        '/' => array(
            'classname' => 'sso\action\Index',
            'filter' => ''
        ),
        '/demo' => array(
            'classname' => 'demo\action\DemoAction'
        ),
        '/authorize' => array(
            'classname' => 'sso\action\Authorize'
        ),
        '/token' => array(
            'classname' => 'sso\action\Token'
        ),
        '/info' => array(
            'classname' => 'sso\action\Info'
        ),
        '/verify' => array(
            'classname' => 'sso\action\Verify'
        ),
        '/agreement' => array(
            'classname' => 'sso\action\Agreement'
        ),
        '/redirect' => array(
            'classname' => 'sso\action\Redirect'
        ),
        '/test' => array(
            'classname' => 'sso\test\TestAction'
        )
    ),
    'stores' => array(
        'default' => array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'root',
            'password' => 'yuiopas',
            'schema' => 'laysoft'
        ),
        'mysql' => array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'root',
            'password' => 'yuiopas',
            'schema' => 'laysoft'
        ),
        'pdo' => array(
            'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=laysoft',
            'username' => 'root',
            'password' => 'yuiopas',
            'schema' => 'laysoft'
        ),
        'memcache' => array(
            'host' => '127.0.0.1',
            'port' => 11211
        ),
        'redis' => array(
            'host' => '127.0.0.1',//192.168.159.127
            'port' => 6379
        ),
        'mongo' => array(
            'host' => '127.0.0.1',
            'port' => 27017,
            'username' => 'lay',
            'password' => '123456',
            'schema' => 'laysoft'
        )
    )
);
?>
