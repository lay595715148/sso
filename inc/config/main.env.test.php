<?php
use lay\util\Logger;
use config\C;
use config\T;

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
    'actions' => array(
        '/' => array(
            'classname' => 'sso\action\Index'
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
        '/redirect' => array(
            'classname' => 'sso\action\Redirect'
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
