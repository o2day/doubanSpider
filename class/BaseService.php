<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 18:06
 */

class BaseService {

    const COOKIEFILE = BASE_PATH . '/tmp/cookie.file';

    public $config = NULL;
    public $redis = NULL;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function http_get($url,$refer = false,$cookieFile = self::COOKIEFILE,$proxy = false)
    {

        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36');
        if ($refer) {
            curl_setopt($oCurl, CURLOPT_REFERER, $refer);
        }
        curl_setopt($oCurl, CURLOPT_ENCODING, 'gzip,deflate,br');
        //curl_setopt($oCurl, CURLOPT_HEADER, 1);

        curl_setopt($oCurl, CURLOPT_HTTPHEADER,
            array(
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'Accept-Encoding: gzip, deflate, br',
                'Accept-Language: zh-CN,zh;q=0.9',
                'Cache-Control: max-age=0'
            )
        );

        curl_setopt($oCurl, CURLOPT_COOKIEJAR, self::COOKIEFILE);
        curl_setopt($oCurl, CURLOPT_COOKIEFILE, self::COOKIEFILE);

        if ($proxy) {
            curl_setopt($oCurl, CURLOPT_PROXY, '127.0.0.1');
            curl_setopt($oCurl, CURLOPT_PROXYPORT, "8888");
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
//            throw new Exception(
//                'HttpCode' . $aStatus['http_code']
//            );
            return intval($aStatus['http_code']);
        }
    }

    public function http_post($url,$params,$referer = false,$header = false,$proxy = false){
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }

        if(is_string($params)){
            $strPost = $params;
        }else{
            $strPost = http_build_query($params);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($oCurl,CURLOPT_POST,true);
        curl_setopt($oCurl,CURLOPT_POSTFIELDS,$strPost);

        curl_setopt($oCurl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36');
        if($referer){
            curl_setopt($oCurl,CURLOPT_REFERER,$referer);
        }
        curl_setopt($oCurl, CURLOPT_ENCODING, 'gzip,deflate,br');

        if($header){
            curl_setopt($oCurl,CURLOPT_HEADER,1);
        }


        curl_setopt($oCurl,CURLOPT_HTTPHEADER,
            array(
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Encoding: gzip, deflate, br',
                'Accept-Language: zh-CN,zh;q=0.9',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                'Upgrade-Insecure-Requests: 1'
            )
        );

        curl_setopt($oCurl,CURLOPT_COOKIEJAR,self::COOKIEFILE);
        curl_setopt($oCurl,CURLOPT_COOKIEFILE,self::COOKIEFILE);

        if($proxy){
            curl_setopt($oCurl, CURLOPT_PROXY, '127.0.0.1');
            curl_setopt($oCurl, CURLOPT_PROXYPORT, "8888");
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return $sContent;
        }
    }

    public function _xml($content){
        return simplexml_load_string($content);
    }

    /**
     * @param $content
     * @param $url
     * @return bool|string
     */
    public function _gz($content,$url){
        $_tmp = explode('/',$url);
        $gzFile = end($_tmp);

        file_put_contents(BASE_PATH . '/tmp/' . $gzFile,$content);
        $gzFile = BASE_PATH . '/tmp/' . $gzFile;
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $gzFile);
        $file = gzopen($gzFile, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        while(!gzeof($file)) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        fclose($out_file);
        gzclose($file);
        return file_get_contents($out_file_name);
    }

    public function debug($msg){
        echo $msg . PHP_EOL;
    }

}