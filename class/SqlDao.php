<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 18:22
 */

class SqlDao {

    private $mysql;
    private $sqlStr;

    public function __construct()
    {
        $config = include BASE_PATH . '/config/config.php';
        $this->mysql = sMysql::get_instance($config);
    }

    /**
     * @param $fields
     * @return $this
     */
    public function select($fields = "*"){
        $this->sqlStr = "";
        if(is_string($fields)){
            $this->sqlStr = "select $fields ";
        }else{
            $this->sqlStr = "select " . implode(",",$fields) . "   " ;
        }
        return $this;
    }

    public function from($table){
        $this->sqlStr .= " from $table ";
        return $this;
    }

    public function insert($params,$table){
        $_keys = $_values = "";
        foreach($params as $k => $v){
            $_keys .= "$k,";
            $_values .= "'$v',";
        }

        $_keys = trim($_keys,",");
        $_values = trim($_values,",");

        $this->sqlStr = sprintf("insert into %s ($_keys) values ($_values)",$table);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function update($table){
        $this->sqlStr = "update $table ";
        return $this;
    }

    /**
     * @param $params
     * @return string
     */
    public function set($params){
        if(is_string($params)){
            $paramsStr = $params;
        }else{
            $paramsStr = "";
            foreach($params as $k => $v){
                $paramsStr .= " $k = '$v',";
            }
            $paramsStr = trim($paramsStr,",");
        }
        $this->sqlStr .= " set " . $paramsStr;
        return $this;
    }

    public function into($table){
        $this->sqlStr = sprintf($this->sqlStr,$table);
        return $this;
    }

    public function where($params){
        if(is_string($params)){
            $this->sqlStr .= " where $params";
        }else{
            $_str = "";
            foreach($params as $k => $v){
                $_str .= sprintf(" and %s = '%s' ",$k,$v);
            }
            $this->sqlStr .= " where 1 = 1 " . $_str;
        }
        return $this;
    }

    /**
     * @param bool $debug
     * @return array
     */
    public function query($debug = false){
        if($debug){
            exit($this->sqlStr);
        }

        for($i = 0;$i<2;$i++){

            $result = $this->mysql->query($this->sqlStr);
            if($result){
                $this->sqlStr = '';
                return $result->fetchAll();
            }else{
                if($this->mysql->errorCode() ==  2006 || $this->mysql->errorCode() == 2013){
                    $this->mysql = null;
                    $this->mysql = mysql::get_instance($this->config);
                }else{
                    var_dump($this->mysql->errorInfo());
                    exit("sql query error");
                }
            }
        }
    }


}