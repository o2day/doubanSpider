<?php

class sMysql{

    private static $_mysql = null;

    public static function get_instance($config){
        if(!self::$_mysql instanceof self){
            $db_uri = sprintf('mysql:host=%s;dbname=%s',$config->mysql->host,$config->mysql->database);
            return self::$_mysql = new PDO($db_uri, $config->mysql->user, $config->mysql->password);
        }else{
            return self::$_mysql;
        }
    }
}
