<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 18:05
 */

define('BASE_PATH',dirname(__FILE__));

class go {

    public $config ;
    public $SpiderService;

    public function __construct()
    {
        $this->config = include(BASE_PATH . '/config/config.php');
        $this->loadLibrary();
        $this->SpiderService = new SpiderService($this->config);
    }

    /**
     * 加载class目录
     */
    public function loadLibrary(){
        foreach($this->config as $k => $v){
            if(preg_match('/Dir/',$k)){
                foreach(scandir($v) as $file){
                    if(preg_match('/.php/i',$file)){
                        include($v.$file);
                    }
                }
            }
        }
    }

    public function start(){
        $this->SpiderService->getMainSiteMap()->getSubSiteMap();
    }

    /**
     * @param string $category
     */
    public function worker($category = 'BOOK'){

        switch($category){
            case 'BOOK':
            case 'MOVIE':
                    $this->SpiderService->startWork($category);
                break;
            default:
                    $this->SpiderService->debug('no such category') ;
                break;
        }
    }


}

switch($argv[1]){
    //获取sitemap
    case 'sitemap':
        (new go())->start();
        break;
    //获取电影相关信息
    case 'work':
            (new go())->worker('MOVIE');
        break;
    default:
        echo "";
        break;
}



/*
$go = new go();
$go->start();
*/
