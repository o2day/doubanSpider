<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/16 0016
 * Time: 22:44
 */

class Movie {

    public $id,$name,$url,$datepublished,$type,$ratecount,$ratevalue,$genre,$lang,$img;

    const MOVIE_FIELD_NAME = 'name';
    const MOVIE_FIELD_URL  = 'url';
    const MOVIE_FIELD_DATEPUBLISH = 'datepublished';
    const MOVIE_FIELD_NODE = 'node';
    const MOVIE_FIELD_RATECOUNT = 'ratecount';
    const MOVIE_FIELD_RATEVALUE = 'ratevalue';
    const MOVIE_FIELD_GENRE = 'genre';
    const MOVIE_FIELD_LANG = 'lang';
    const MOVIE_FIELD_IMG = 'img';

    const TABLE = 'tbmovie';

    public static function findFirst($sql){
        $t = (new SqlDao())->select('*')->from(self::TABLE)->where($sql)->query();
        if(count($t)){
            return $t[0];
        }else{
            return array();
        }
    }

    public static function count($sql){
        $t = (new SqlDao())->select("count(1) cnt")->from(self::TABLE)->where($sql)->query();
        return $t[0]['cnt'];
    }

    public static function update($params,$where){
        $t = (new SqlDao())->update(self::TABLE)->set($params)->where($where)->query(false);
        return $t;
    }


    public static function add($params){
        return $t = (new SqlDao())->insert($params,self::TABLE)->query(false);
    }

}