<?php
use lay\util\Logger;
use config\C;
use config\T;

return array(
    'logger' => array(Logger::L_ALL & ~Logger::L_INFO, false, 0),
    'actions' => array(
        '/' => array(
            'classname' => 'sso\action\Index'
        ),
        '/authorize' => array(
            'classname' => 'sso\action\Authorize'
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
