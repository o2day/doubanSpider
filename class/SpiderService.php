<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 18:05
 */

class SpiderService extends BaseService {

    const DOUBAN_SITEMAP_URL = 'https://www.douban.com/sitemap_index.xml';

    const BOOK_DOUBAN_LIST = 'BOOK_DOUBAN_LIST';
    const MOVIE_DOUBAN_LIST= 'MOVIE_DOUBAN_LIST';

    private $mainSiteMap;

    /**
     *
     */
    public function getMainSiteMap(){
        $this->mainSiteMap  = $this->_getSiteMap(self::DOUBAN_SITEMAP_URL);
        return $this;
    }

    public function getSubSiteMap(){
        foreach($this->mainSiteMap as $gzfile){
            $this->_getSiteMap($gzfile->loc);
        }
    }

    /**
     * @param $url
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function _getSiteMap($url){

        if($content = Cache::get($url)){

        }else{
            $content = $this->http_get($url);
            Cache::set($url,$content);
        }

        $_t = explode('.',$url);
        $ext = strtolower(end($_t));

        switch($ext){
            case 'xml':
                    return $content = $this->_xml($content);
                break;
            case 'gz':
                    $content = $this->_xml($this->_gz($content,$url));
                    $this->addRedis($content);
                break;
        }
    }

    public function addRedis($content){

        $this->redis = mRedis::get_instance($this->config);

        foreach($content as $node){

            $loc = end($node->loc);

            if(preg_match('/book.douban.com\/subject/i',$loc)){
                $this->redis->lPush(
                    self::BOOK_DOUBAN_LIST,
                    $loc
                );
                echo $loc . ' booklist '. PHP_EOL;
            }else if(preg_match('/movie.douban.com\/subject/i',$loc)){
                $this->redis->lPush(
                    self::MOVIE_DOUBAN_LIST,
                    $loc
                );
                echo $loc . ' movielist '. PHP_EOL;
            }else{
                echo $loc . ' not meet '. PHP_EOL;
            }
        }

    }

    public function startWork($category){
        $this->redis = mRedis::get_instance($this->config);
        while(true){
            $t = $this->redis->brPop(
                $this->_category($category),
                0
            );
            if(count($t)){
                $_func = ucfirst($category);
                echo 'get ' . $t[1] . PHP_EOL;
                $this->$_func($t[1]);
            }
        }
    }

    public function Book($url){
//        $content = $this->http_get($url);

    }

    public function Movie($url){

        $node = $this->getNode($url);
        if($this->checkMovie($node)){
            return;
        }

        if($content = Cache::get($url)){

        }else{
            $content = $this->http_get($url);
            if(preg_match('/^\d+$/',$content)){
                switch($content){
                    case 404:
                            echo $url . ' not existed' . PHP_EOL;
                            return;
                        break;
                    case 302:
                            throw new Exception(
                                '302'
                            );
                            return;
                        break;
                    default:
                        throw new Exception(
                            $content
                        );
                        break;
                }
            }else{
                Cache::set($url,$content);
            }
        }


        $data = $this->_movie($content,$url);
        $this->addMovie($data);

	    echo 'sleep 15 seconds' . PHP_EOL;
	    sleep(15);
    }

    public function _movie($content,$url){

        $html = str_get_html($content);

        $idJson = $html->find(
            "script[type=application/ld+json]",0
        )->innertext;

        $json = json_decode($idJson);

        $infoStr = $html->find("div[id=info]",0)->plaintext;
        $_start = mb_stripos($infoStr,"语言");
        $_end = mb_stripos($infoStr,"上映日期");
        $lang = trim(mb_substr($infoStr,$_start+3,($_end-$_start)-4));

        return array(
            Movie::MOVIE_FIELD_NAME => $json->name,
            Movie::MOVIE_FIELD_URL  => $json->url,
            Movie::MOVIE_FIELD_IMG  => $json->image,
            Movie::MOVIE_FIELD_DATEPUBLISH  => $json->datePublished,
            Movie::MOVIE_FIELD_GENRE=> implode(',',$json->genre),
            Movie::MOVIE_FIELD_RATECOUNT    => $json->aggregateRating->ratingCount,
            Movie::MOVIE_FIELD_RATEVALUE    => $json->aggregateRating->ratingValue,
            Movie::MOVIE_FIELD_LANG         => $lang,
            Movie::MOVIE_FIELD_NODE         => $this->getNode($url)
        );
    }

    public function addMovie($params){
        return Movie::add($params);
    }

    public function checkMovie($node){
        $sql = sprintf(" node = '%s'",$node);
        if(Movie::findFirst($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $category
     * @return mixed
     * @throws Exception
     */
    public function _category($category){
        $arr = array(
            'BOOK'  => self::BOOK_DOUBAN_LIST,
            'MOVIE' => self::MOVIE_DOUBAN_LIST
        );
        if(isset($arr[$category])){
            return $arr[$category];
        }else{
            throw new Exception(
                'no such category'
            );
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    public function getNode($url){
        preg_match_all(
            "/\d+/i",$url,$match
        );
        return $match[0][0];
    }

}
