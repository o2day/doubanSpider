<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/16 0016
 * Time: 11:50
 */

class Cache {

    public static function get($key){
        $cacheFile = self::_key($key);
        if(file_exists($cacheFile)){
            return file_get_contents($cacheFile);
        }else{
            return false;
        }
    }

    public static function set($key,$value){
        $cacheFile = self::_key($key);
        return file_put_contents(
            $cacheFile,
            $value
        );
    }

    public static function _key($key){
        return BASE_PATH . '/tmp/' . md5($key) ;
    }

}