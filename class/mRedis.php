<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/16 0016
 * Time: 17:56
 */

class mRedis
{
    private static $_redis;
    /**
     * @param $config
     * @return bool|Redis
     */
    public static function get_instance($config)
    {
        if (extension_loaded('redis')) {
            if (!(self::$_redis instanceof self)) {
                try {
                    self::$_redis = new Redis();
                    self::$_redis->connect($config->redis->host, $config->redis->port);
                    if ($config->redis->auth != '') {
                        self::$_redis->auth(trim($config->redis->auth));
                    }
                } catch (Exception $ex) {
                    var_dump($ex->getMessage());
                    exit('redis error');
                }
            }
            return self::$_redis;
        } else {
            exit('redis not loaded');
        }
    }

}