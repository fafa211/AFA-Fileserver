<?php

/**
 * 系统常用静态方法
 * @author zhengshufa <22575353@qq.com>
 * @copyright afaphp.com
 */
class F{
    
    /**
     * 从数组中查找需要的数据
     * @param string|int $needle
     * @param array $arr
     * @return string: for show string
     */
    public static function findInArray($needle, $arr){
        if (isset($arr[$needle])) return $arr[$needle];
        if (strpos($needle, ',') !== FALSE){
            $needle_arr = explode(',', $needle);
            
            $rs = array();
            foreach ($needle_arr as $v){
                if (isset($arr[$v])) array_push($rs, $arr[$v]);
            }
            if (!empty($rs)) return join(',', $rs);
        }
        return $needle;
    }
    
    /**
     * 手动载入module文件方法
     * @param string $class_name 类名称
     * @param string $module: module名称
     */
    public static function load($class_name, $module = null){
        global $modules;
        if (!isset($modules[$module])) return false;
        if (class_exists($class_name, false)) return true;
        
        if (strpos($class_name, '_') === false){
            $file = self::find_file('helper', $class_name, $module);
        }else{
            $lastpos = strrpos($class_name, '_');
            $suffix = substr($class_name, $lastpos+1);
            $file = '';
            $class_name = substr($class_name, 0, $lastpos);
            
            $file = self::find_file(strtolower($suffix), $class_name, $module);
        }
        if ($file) {
            include($file);
            return true;
        }
        return false;
    }
    
    /**
     * 手动载入文件
     * @param string $type 文件类型
     * @param string $class 类名称
     * @param string $module 模块名
     */
    public static function find_file($type, $class, $module = NULL){
        
        $filename = $class.EXT;
        
        switch ($type){
            case 'helper':
            case 'controller':
            case 'model':
                $dir = ($module?MODULEPATH.$module.DIRECTORY_SEPARATOR:APPPATH).$type.DIRECTORY_SEPARATOR;
                break;
            case 'view':
                $dir = ($module?MODULEPATH.$module.DIRECTORY_SEPARATOR:APPPATH).$type.DIRECTORY_SEPARATOR;
                if (!file_exists($dir.$filename) && $module){
                    $dir .= $module.DIRECTORY_SEPARATOR;
                }
                break;
            case 'class':
                $dir = SYSTEMPATH.$type.DIRECTORY_SEPARATOR;
                break;
            case 'config':
                $dir = APPPATH.$type.DIRECTORY_SEPARATOR;
                break;
            case 'driver':
                $dir = SYSTEMPATH.'class'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR;
                break;
            default:
                return false;
        }
        
        return $dir.$filename;
    }
    
    /**
     * 创建对象，调用其他模型类，并生成对象
     * @param string $module: module名称
     * @param string $class_name 类名称
     * @param string|in|array 类构造函数的参数
     * @return object 返回一个对象
     */
    public static function object($module, $class_name, $args){
        $rs = self::load($class_name, $module);
        if($rs === false) exit("Load $class_name failure!");
        $class = new ReflectionClass($class_name);
        return $class->newInstance($args);
    }
    
    /**
     * 读取配置
     */
    public static function config($str)
    {
        if (isset($GLOBALS['config'][$str])) return $GLOBALS['config'][$str];
        if (strpos($str, '.') === false){
            $main = $str;
            $sub = null;
        }else {
            list($main, $sub) = explode('.', $str);
        }
        if (isset($GLOBALS['config'][$main]) && isset($GLOBALS['config'][$main][$sub])) return $GLOBALS['config'][$main][$sub];
        if (isset($GLOBALS['config'][$main])) {
            return $GLOBALS['config'][$main];
        }else{ 
            //支持在application下文件夹config设置各自的配置文件
            $file = APPPATH.'config'.DIRECTORY_SEPARATOR.$main.EXT;
            if (file_exists($file)) {
                $GLOBALS['config'][$main] = include $file;
                if (isset($GLOBALS['config'][$main][$sub])) return $GLOBALS['config'][$main][$sub];
                return $GLOBALS['config'][$main];
            }
        }
        
        return $GLOBALS['config'];
    }
    
    /**
     * 获取客户端IP
     */
    public static function getIp() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if( isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP']) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches) ) {
            foreach ($matches[0] as $value_ip) {
                if (!preg_match('/^(10|172\.16|192\.168)\./', $value_ip)) {
                    $ip = $value_ip;
                    break;
                }
            }
        }
        return $ip;
    }
    
    /**
     * 字符串加密、解密函数
     *
     * @param    string    $string        字符串
     * @param    string    $operation    ENCODE为加密，DECODE为解密，可选参数，默认为DECODE，
     * @param    string    $key        密钥：数字、字母、下划线
     * @param    string    $expiry        过期时间
     * @return    string
     */
    public static function authstr($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $key_length = 5; // 随机密钥长度 取值 0-32
        $key = md5( $key != '' ? $key : self::config('authkey'));
        $fixedkey = md5($key);
        $egiskeys = md5(substr($fixedkey, 16, 16));
        $runtokey = $key_length ? ($operation == 'DECODE' ? substr($string, 0, $key_length) : substr(md5(microtime(true)), -$key_length)) : '';
        $keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
        $string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));
    
        $i = 0;
        $result = '';
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i++) {
            $result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
        }
        if($operation == 'ENCODE') {
            return $runtokey . str_replace('=', '', base64_encode($result));
        } else {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        }
    }
    
    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str
     * @param int $start
     * @param int $length
     * @param string $charset
     * @param boolean $suffix
     * @return string
     */
    static function substr($str, $start = 0, $length, $charset = "utf-8", $suffix = TRUE) {
        $suffix_str = $suffix ? '…' : '';
        if(function_exists('mb_substr')) {
            return mb_substr($str, $start, $length, $charset) . $suffix_str;
        } elseif(function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset) . $suffix_str;
        } else {
            $pattern = array();
            $pattern['utf-8'] = '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/';
            $pattern['gb2312'] = '/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/';
            $pattern['gbk'] = '/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/';
            $pattern['big5'] = '/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/';
            preg_match_all($pattern[$charset], $str, $matches);
            $slice = implode("", array_slice($matches[0], $start, $length));
            return $slice . $suffix_str;
        }
    }
    
    /**
     * 信息提示
     * @param string $message
     * @param string $url
     * @param number $refresh
     * @param boolean $exit
     */
    static function tip($message, $url = '', $refresh = 3, $exit = false){
        header("HTTP/1.1 200 OK");
        header("Status: 200");
        header("Content-Type: text/html; charset=UTF-8");
        if ($url){
            header("refresh:$refresh;url=$url");
        }
        if (preg_match('/MSIE/i', input::server('HTTP_USER_AGENT'))) {
            echo str_repeat(" ", 512);
        }
        echo $message;
        if ($exit) exit();
    }
    
    /**
     * 上传图片到图片服务器
     * @param array $file : upload file
     * @param array $arr
     * @param array $quality 质量，默认90
     * 单张图片：$arr = array('w'=>120, 'h'=>90); 或 $arr = array(array(120,90); 或 array(120,90);
     * 大小图片，即两张图片：$arr = array(array(400,300),array(120,90)); array(400,300)为大图宽高，array(120,90)为小图宽高
     * 大中小图，即三张图片：$arr = array(array(1200,900),array(400,300),array(120,90)); 依次为大中小图的尺寸大小
     * @return string $url 或 略缩图url
     * 当为多张图片时，则只返回最小图片,最小图片名称以 s_ 前缀。
     * 大图以 b_ 前缀
     * 中图以 m_ 前缀，大图和中图 需要根据小图地址进行转化而得到，如：str_replace('s_','b_', $smallpic);
     */
    public static function uploadPic($file, $arr = array(), $quality = 90){
        $fullfile = Upload::save($file);
        $image = Image::instance($fullfile);
        $return_url = '';//返回的url
        $has_small = false;//是否有略缩图
        
        if (isset($arr['w']) || isset($arr['h']) || is_numeric($arr[0])){
            $width = @$arr['w'] || @$arr[0] || NULL;
            $height = @$arr['h'] || @$arr[1] || NULL;
            $image->resize($width, $height)->save(NULL, $quality);
        }else{
            $count = count($arr);
            if (1 == $count){
                $arr = $arr[0];
                $image->resize(isset($arr[0])?$arr[0]:null, isset($arr[1])?$arr[1]:null)->save(NULL, $quality);
            }elseif (2 == $count){
                $image->resize(@$arr[0][0],@$arr[0][1])->save(str_replace('/sc_', '/b_', $fullfile), $quality);
                $image->resize(@$arr[1][0],@$arr[1][1])->save(str_replace('/sc_', '/s_', $fullfile), $quality);
                $has_small = true;
            }elseif (3 == $count){
                $image->resize(@$arr[0][0],@$arr[0][1])->save(str_replace('/sc_', '/b_', $fullfile), $quality);
                $image->resize(@$arr[1][0],@$arr[1][1])->save(str_replace('/sc_', '/m_', $fullfile), $quality);
                $image->resize(@$arr[2][0],@$arr[2][1])->save(str_replace('/sc_', '/s_', $fullfile), $quality);
                $has_small = true;
            }else{
                return false;
            }
        }
        if ($has_small){
            $filesmall = str_replace('/sc_', '/s_', $fullfile);
            $return_url = str_replace(DOCROOT.DIRECTORY_SEPARATOR, F::config('domain'), $filesmall);
            //删除原图
            unlink($fullfile);
        }else{
            $return_url = str_replace(DOCROOT.DIRECTORY_SEPARATOR, F::config('domain'), $fullfile);
        }
        return $return_url;
    }
    
    /**
     * 记录运行时的时间点
     * @param string $mark 记录标示
     * @return array $bencharr  返回所有运行时间点信息
     */
    public static function benchmark($mark = ''){
        static $bencharr;
        
        if (empty($bencharr)){
            $bencharr = array();
        }
        if ($mark){
            $bencharr[$mark] = array('time'=>round(microtime(true)-AFA_START_TIME, 6), 'memory'=>F::convert(memory_get_usage()));
        }else{
            array_push($bencharr, array('time'=>round(microtime(true)-AFA_START_TIME, 6), 'memory'=>F::convert(memory_get_usage())));
        }
        return $bencharr;
    }

    /**
     * @param $size 大小字节数量
     * @return string
     */
    public static function convert($size){
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * 输出性能调试信息
     * @param Request $request
     */
    public static function debug(Request $request){

        if(DEBUG === 0){
            return ;
        }

        global $cController, $cMethod;

        //记录最后运行信息
        $benmark = F::benchmark('end');
        //url信息
        $becnmark_str_arr = array('url: '.$request->url);
        $becnmark_str_arr['start'] = 'Start: time: ' . AFA_START_TIME . ' Start Memory: ' . F::convert(AFA_START_MEMORY);
        foreach ($benmark as $k => $v) {
            $becnmark_str_arr[$k] = $k . ": Time " . $v['time'] . ' s Memory: ' . $v['memory'];
        }


        if(DEBUG === 1) {//浏览器中直接输出
            echo '<hr />';
            echo join('<br />', $becnmark_str_arr);
            echo '<br />';
            return ;
        }
        if(DEBUG === 2){//输出到性能调试日志中
            $logPath = PROROOT.'/runtime/benchmarklog/';
            $fileName = 'log'.date('Y-m-d');
            $logs = new Logs($logPath, $fileName);

            $logContent = join("\n", $becnmark_str_arr);

            $logs->LogInfo($logContent);
        }

    }

    /**
     * 因自带的json_encode会默认把中文字符转化为\uXXX的形式
     * 此方法是为了不对中文字符做转移的json_encode
     * @param $input string/array
     * @return string json encoded string
     */
    public static function json_encode($input){

        // // 从 PHP 5.4.0 起, 增加了这个选项
        if(defined(JSON_UNESCAPED_UNICODE)){
            return json_encode($input, JSON_UNESCAPED_UNICODE);
        }
        //PHP5.4以下版本
        if(is_string($input)){
            $text = $input;
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            return '"' . $text . '"';
        }else if(is_array($input) || is_object($input)){
            $arr = array();
            $is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
            foreach($input as $k=>$v){
                if($is_obj){
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                }else{
                    $arr[] = self::json_encode($v);
                }
            }
            if($is_obj){
                return '{' . join(',', $arr) . '}';
            }else{
                return '[' . join(',', $arr) . ']';
            }
        }else{
            return $input . '';
        }

    }

    /**
     * @param $key 通过key读取Token
     * @return token
     */
    public static function getToken($key){
        if(!empty($key)){
            //账户key授权验证
            $keystr = F::authstr($key, 'DECODE');
            $keyarr = explode(',', $keystr);

            if (count($keyarr) > 1 && is_numeric($keyarr[0]) && $keyarr[0]) {
                $account_id = $keyarr[0]; //为授权用户 ID
                return F::authstr($account_id.','.time(), 'ENCODE');
            }
        }
        return '';
    }
}

/**
 * @param $str
 */
function _e($str){
    $param = func_get_args();
    $param_num = func_num_args();
    if(1 == $param_num) {
        echo $str;
    }elseif(2 == $param_num){
        printf($str, $param[1]);
    }elseif(3 == $param_num){
        printf($str, $param[1],  $param[2]);
    }else{
        printf($str, $param[1],  $param[2],  $param[3]);
    }
    return true;
}