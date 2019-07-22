<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 18:13
 */


$arr = array(
    'mysql'    => array(
        'host'      => '127.0.0.1',
        'user'      => 'doubanSpider',
        'password'  => '',
        'database'  => 'doubanSpider'
    ),
    'redis'     => array(
        'host'  => '127.0.0.1',
        'port'  => '6379',
        'auth'  => '',
    ),
    'libraryDir'   => BASE_PATH . '/class/',
    'utilsDir'     => BASE_PATH . '/utils/',
    'modelDir'     => BASE_PATH . '/model/',
);

return json_decode(
    json_encode(
        $arr
    )
);
