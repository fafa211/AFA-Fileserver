<?php if (!defined('AFA')) die();

/**
 * 数据库操作类 db 使用->PDO 接口
 * 支持多个数据库，支持一主多从
 * @see        https://github.com/fafa211/AFA-PHP/blob/master/system/class/db.php
 * @author     郑书发 <22575353@qq.com>
 * @version    2.0
 * @category   db
 * @package    Classes
 * @copyright  Copyright (c) 2015-2020 afaphp.com
 * @license    http://www.afaphp.com/license.html
 */

class db
{   
    //存储自身对象
	private static $_this = array(); 
	//存储当前使用的数据库链接
    private $db;
    //读取数据方式FETCH_ASSOC \FETCH_NUM \FETCH_BOTH \FETCH_OBJ
    private $fetch_mode = PDO::FETCH_ASSOC;
    
    //存储配置文件, 读服务器配置，写服务器配置
    private $config = array();
    //存放写服务器链接
    private $write_db; 
    //存放读链接
	private $read_db;
	//是否存在从库，默认无从库
	private $has_slave = false; 
	
    /**
     * 私有构造函数
     * @return  void
     */
    private function __construct($config = 'default') 
    {
        $config_arr = F::config('database.'.$config);
        //master配置是必不可少的
        if (!isset($config_arr['master']) || empty($config_arr['master'])) {
            //没有模块数据库，则用默认数据库
            $config_arr = F::config('database.default');
        }
        $this->config['write'] = $config_arr['master'];
        $length = count($config_arr);
        if ($length == 1){
            $this->config['read'] = $config_arr['master'];
        }else{
            //从数据库个数
            $slave_length = $length-1;
            if ($slave_length == 1){
                $this->config['read'] = $config_arr['slave'];
            }else{
                //从slave中随机读取一个
                $rand = rand(1, $slave_length);
                $this->config['read'] = $config_arr['slave'.$rand];
            }
            $this->has_slave = true;
        }
        
    }
   
    /**
     * create database instance
     * @param string $config: 默认值为 default
     * @return pdoquery class object
     */
    public static function instance($config = 'default')
    {
        if (! isset(self::$_this[$config])) {
            self::$_this[$config] = new self($config);
        }
        return self::$_this[$config];
    } 
    
    /**
     * 查询
     * @param  string $sql
     * @return  array 查询得到的数据数组
     */
    public function query($sql, $master = false)
    {	
    	$this->setServer($master);
        $this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $rs = $this->db->query($sql);
        $rs->setFetchMode($this->fetch_mode);
        return $rs->fetchAll();
    }
    
   
    /**
     * 更新/插入数据
     * @param  string $sql
     * @return  boolean 成功true
     */
    public function exec($sql, $master = true)
    {
    	$this->setServer($master);
        return $this->db->exec($sql);
    }
   
    /**
     * @return  最新插入的数据ID
     */
    public function getId()
    {
    	$this->setServer(true);
        return $this->db->lastInsertId();
    }

    /**
     * 得到查询结果中的第一行第一列数据
     *
     * @param  string $sql
     * @param $master 默认为从数据库读取
     * @return  string
     */
    public function getOne($sql, $master = false)
    {
    	$this->setServer($master);
        $rs = $this->db->query($sql);
        return $rs->fetchColumn();
    } 
    
    /**
     * 得到查询结果中的第一行数据
     *
     * @param  string $sql
     * @return  string
     */
    public function getOneResult($sql)
    {
    	$this->setServer($sql);
        $rs = $this->db->query($sql);
		
        $rs->setFetchMode($this->fetch_mode);
        return $rs->fetch();
    }
    
    /**
     * 事务处理，执行一系列更新或插入语句
     */
    public function transaction($sqlQueue)
    {
        if(count($sqlQueue)>0)
        {
            //$this->result->closeCursor();
            try
            {
                $this->setServer(true);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $this->db->beginTransaction();
                foreach ($sqlQueue as $sql)
                {
                    $this->db->exec($sql);
                }
                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollBack();
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 处理mysql读写分离
     * @param $master 是否使用主库链接
     */
     private function setServer($master = TRUE)
    {	
    	if ($master || !$this->has_slave){
    	    if ($this->write_db) {
    	        $this->db = $this->write_db;
    	    } else {
    	        $this->db = $this->write_db = $this->connect($this->config['write']);
    	    }
    	}else{
    	    if ($this->read_db) {
    	        $this->db = $this->read_db;
    	    } else {
    	        $this->db = $this->read_db = $this->connect($this->config['read']);
    	    }
    	}
    }
    
    /**
     * 处理mysql连接
     * @param array $config
     */
    private static function connect($config){
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
        $db = new PDO($dsn, $config['user'], $config['password'], array(PDO::ATTR_PERSISTENT => $config['conmode']));
        $db->exec('SET NAMES ' . $config['charset']);
        return $db;
    }
       
       
}


/*********************************************************************************
 * sql build 类， 不支持子查询
 * $Author:郑书发
 * $Dtime:2015-10-10
 ***********************************************************************************/
class sql {
    //SQL语句
    private $sql = '';
    //SQL类型
    private $type = '';
    private $filed = '';
    private $where = '';
    private $table = '';
    private $groupby = '';
    private $orerby = '';
    private $limit = '';
    private $set = '';
    private $innerarr = array();
    private $leftarr = array();
    private $rightarr = array();
    
    /**
     * 
     * @param string $type: 类型总共三个 SELECT,INSERT,UPDATE,DELETE
     */
    public function __construct($type = 'SELECT'){
        $this->type = strtoupper($type); 
    }
    
    /**
     * 组装读取的字段
     * @param string|array $filed
     * @param string $from :table name
     * @param array $where array('key' => 'val')
     * @return sqlbuild object
     */
    public static function select($filed = '', $from = '', $where = array()){
        $_this = new self('SELECT');
        $_this->filed($filed);
        $_this->from($from);
        $_this->where($where);
        return $_this;
    }
    
    /**
     * 插入语句
     * @param array $val array('key' => 'val')
     * @param string $table
     * @return sqlbuild object
     */
    public static function insert($val = array(), $table = ''){
        $_this = new self('INSERT');
        $_this->set($val);
        $_this->table($table);
        return $_this;
    }
    
    /**
     * 更新语句
     * @param array $val array('key' => 'val')
     * @param string $table
     * @param array $where array('key' => 'val')
     * @return sqlbuild object
     */
    public static function update($val = array(), $table = '', $where = array()){
        $_this = new self('UPDATE');
        $_this->set($val);
        $_this->table($table);
        $_this->where($where);
        return $_this;
    }
    
    /**
     * 删除语句
     * @param string $table
     * @param array $where array('key' => 'val')
     * @return sqlbuild object
     */
    public static function delete($table = '', $where = array()){
        $_this = new self('DELETE');
        $_this->table($table);
        $_this->where($where);
        return $_this;
    }
    
    /**
     * SQL组装-组装AND符号的WHERE语句
     * 返回：WHERE a = 'a' AND b = 'b'
     * @param string|array $val array('key' => 'val')
     * @param string $flag : = > < >= <=
     * @param string|int $value
     * @param string $connect:链接关键词 ' AND ' 或者 ' OR '
     * @return string
     */
    public function where($arr, $flag = FALSE, $value = '', $connect = ' AND '){
        if (!is_array($arr)) {
            if ($flag){
                if($this->where){
                    $this->where .= $connect.$arr.$flag.self::escape($value);
                }else{
                    $this->where = $arr.$flag.self::escape($value);
                }
            }else{
                if($this->where){
                    $this->where .= $connect.$arr;
                }else{
                    $this->where = $arr;
                }
            }
        }else {
            $temp = array();
            foreach ($arr as $k => $v) {
                //$k为非数字时,为字段名称
                if(is_numeric($k)){
                    if(is_array($v)){
                        if(count($v) == 3) {
                            $this->where($v[0], $v[1], $v[2]);
                        }
                    }else{
                        if(count($arr) == 3){
                            $this->where($arr[0], $arr[1], $arr[2]);
                            break;
                        }else{
                            $this->where($v);
                        }
                    }
                }else {
                    $temp[] = "$k=" . self::escape($v) . "";
                }
            }
            if(!empty($temp)) {
                if ($this->where) {
                    $this->where .= $connect . implode(' AND ', $temp);
                } else {
                    $this->where = implode(' AND ', $temp);
                }
            }
        }
        return $this;
    }
    
    /**
     * where 条件里的 AND 链接，
     * 与where功能相同，实际调用 $this->where($arr, $flag, $value)
     * @param string|array $val array('key' => 'val')
     * @param string $flag : = > < >= <=
     * @param string|int $value
     * @return $this 对象
     */
    public function and_c($arr, $flag = '', $value = ''){
        return $this->where($arr, $flag, $value);
    }
    
    /**
     * where 条件里的 OR 链接，
     * 实际调用 $this->where($arr, $flag, $value, ' OR ')
     * @param string|array $val array('key' => 'val')
     * @param string $flag : = > < >= <=
     * @param string|int $value
     * @return $this 对象
     */
    public function or_c($arr, $flag = '', $value = ''){
        return $this->where($arr, $flag, $value, ' OR ');
    }
    
    /**
     * 设置查询|插入|更新|删除数据表
     * @param string 数据表名
     * @return $this 对象
     */
    public function from($table){
        $this->table = $table;
        return $this;
    }
    
    /**
     * 设置查询|插入|更新|删除数据表
     * 内部实际调用 $this->from($table)
     * @param string 数据表名
     * @return $this 对象
     */
    public function table($table){
        return $this->from($table);
    }
    
    /**
     * 设置查询的字段
     * @param string|array $filed 字段：多个字段用逗号(,)分隔，或者把字段放在一维数组中
     * @return $this object
     */
    public function filed($filed = '*'){
        if (is_array($filed)){
            $this->filed = join(',', $filed);
        }else {
            $this->filed = $filed;
        }
        return $this;
    }
    
    /**
     * 插入|更新时设置字段值
     * @param array $val array('key' => 'val')
     * @return $this object
     */
    public function set($arr){
        if (!is_array($arr) || empty($arr)) return $this;
        $temp = array();
        foreach ($arr as $k => $v) {
            $temp[] = "`$k`=".self::escape($v)."";
        }
        $this->set = implode(',', $temp);
        return $this;
    }
    
    /**
     * 设置inner join 表名与关联字段关系
     * @param string $table:表名
     * @param string $condition:关联关系，如 user.id=userinfo.user_id
     * @return $this object
     */
    public function innerjoin($table, $condition){
        $this->innerarr[] = array('table'=>$table, 'condition'=>$condition);
        return $this;
    }
    
    /**
     * 设置left join 表名与关联字段关系
     * @param string $table:表名
     * @param string $condition:关联关系，如 user.id=userinfo.user_id
     * @return $this object
     */
    public function leftjoin($table, $condition){
        $this->leftarr[] = array('table'=>$table, 'condition'=>$condition);
        return $this;
    }
    
    /**
     * 设置right join 表名与关联字段关系
     * @param string $table:表名
     * @param string $condition:关联关系，如 user.id=userinfo.user_id
     * @return $this object
     */
    public function rightjoin($table, $condition){
        $this->rightarr[] = array('table'=>$table, 'condition'=>$condition);
        return $this;
    }
    
    /**
     * 设置分组字段
     * @param string $groupby:分组字段，多个字段用逗号(,)分隔
     * @return $this object
     */
    public function groupby($groupby){
        $this->groupby = $groupby;
        return $this;
    }
    
    /**
     * 设置排序字段
     * @param string $orderby:比如 id desc
     * @return $this object
     */
    public function orderby($orderby){
        $this->orerby = $orderby;
        return $this;
    }
    
    /**
     * 设置读取条数 LIMIT $offset, $num
     * @param int|string $offset:起始位置
     * @param int $num:结果条数
     * @return $this object
     */
    public function limit($offset = 0, $num = 0){
        $this->limit = $offset.($num?','.$num:'');
        return $this;
    }
    
    /**
     * 设置排序字段
     * @param string $filed:字段
     * @param array $arr:一维数组
     * @return $this object
     */
    public function in($filed, $arr){
        foreach ($arr as $k=>$v){
            $arr[$k] = self::escape($v);
        }
        return $this->where($filed.' IN ('.join(',', $arr).')');
    }
    
    /**
     * 防止SQL注入方法
     * @param string $val:字符串
     * @return string
     */
    public static function escape($val){
        if (is_int($val)) return $val;
        return "'".(get_magic_quotes_gpc()?$val:addslashes($val))."'";
    }
    
    /**
     * 把对象自动解析成SQL语句
     * @return string : sql
     */
    public function __toString(){
        switch ($this->type) {
            case 'SELECT':
                $this->sql = $this->type . ' ' . $this->filed . ' FROM ' . $this->table;
                foreach ($this->innerarr as $v) {
                    $this->sql .= ' INNER JOIN ' . $v['table'] . ' ON ' . $v['condition'];
                }
                foreach ($this->leftarr as $v) {
                    $this->sql .= ' LEFT JOIN ' . $v['table'] . ' ON ' . $v['condition'];
                }
                foreach ($this->rightarr as $v) {
                    $this->sql .= ' RIGHT JOIN ' . $v['table'] . ' ON ' . $v['condition'];
                }
                if ($this->where) {
                    $this->sql .= ' WHERE ' . $this->where;
                }
                if ($this->groupby) {
                    $this->sql .= ' GROUP BY ' . $this->groupby;
                }
                if ($this->orerby) {
                    $this->sql .= ' ORDER BY ' . $this->orerby;
                }
                if ($this->limit) {
                    $this->sql .= ' LIMIT ' . $this->limit;
                }
                break;
            case 'UPDATE':
                $this->sql = $this->type . ' `' . $this->table . '` SET ' . $this->set;
                if ($this->where) {
                    $this->sql .= ' WHERE ' . $this->where;
                }
                if($this->orerby){
                    $this->sql .= ' ORDER BY ' . $this->orerby;
                }
                if ($this->limit) {
                    $this->sql .= ' LIMIT ' . $this->limit;
                }
                break;
            case 'INSERT':
                $this->sql = $this->type . ' INTO `' . $this->table . '` SET ' . $this->set;
                break;
            case 'DELETE':
                $this->sql = $this->type . ' FROM `' . $this->table . '`';
                if ($this->where) {
                    $this->sql .= ' WHERE ' . $this->where;
                }
                if($this->orerby){
                    $this->sql .= ' ORDER BY ' . $this->orerby;
                }
                if ($this->limit) {
                    $this->sql .= ' LIMIT ' . $this->limit;
                }elseif (empty($this->where)){
                    $this->sql .= ' LIMIT 1';//如果条件为空时，且没有限制条数，则添加限制条数为1条， 防止误删除
                }
                break;
            default:
                break;
        }
        
        return $this->sql;
    }
    
    /**
     * 把对象解析成SQL语句
     * @return string : sql
     */
    public function render(){
        return $this->__toString();
    }
}