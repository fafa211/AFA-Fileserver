<?php if (!defined('AFA')) die();

/**
 * 通用类
 * 里面都是静态方法
 * @copyright zhengshufa
 * @author zhengshufa
 * @createTime 2009-06-23
 */

class common{

	/**
	 * @author zhengshufa
	 * @todo: format date time
	 * @createTime: 2009-09-10
	 */
	public static function formatDate($dateTime, $type=1, $today='', $yesterday='')
	{
		if ($today != '') {
			$date = substr($dateTime,0,10);
			if($date == $today) return '今天';
			if($date == $yesterday) return '昨天';
		}
		$typeArr = array(1=>'Y年m月d日',2=>'y年m月d日',3=>'m月d日',4=>'m月d',5=>'m月d日 H:i');
		$timeArr = explode(' ', $dateTime);
		$tArr1   = explode('-', $timeArr[0]);
		$tArr2   = explode(':', $timeArr[1]);
		return str_replace(array('Y','m','d','H','i'),array($tArr1[0],$tArr1[1],$tArr1[2],$tArr2[0],$tArr2[1]),$typeArr[$type]);
	}

	/**
	 * get client ip address
	 * @return String: $ipAddress
	 */
	public static function getIp(){
		if (isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP'] && strcasecmp($_SERVER['HTTP_CDN_SRC_IP'], "unknown")){
			$ip = $_SERVER['HTTP_CDN_SRC_IP'];
		}elseif(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
			$ip = getenv("HTTP_CLIENT_IP");
		}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
			$ip = getenv("REMOTE_ADDR");
		}else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
			$ip = $_SERVER['REMOTE_ADDR'];
		}else{
			$ip = "unknown";
		}
		return($ip);
	}

	/**
	 * js信息提示
	 * @param string $message:提示信息
	 */
	public static function jsTip($message, $url = ''){
		header('Content-Type:text/html; charset=utf-8');
		echo "<script type='text/javascript'>alert('$message');";
		if ($url){
			echo "window.location='$url';";
		}else {
			echo "window.history.go(-1);";
		}
		echo "</script>";
		exit;
	}

	/**
	 * 执行404错误页面
	 */
	public static function go404($message = ''){
		if (!defined(DEBUG))
			header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		header("Content-Type: text/html; charset=UTF-8");
		if (preg_match('/MSIE/i',input::server('HTTP_USER_AGENT'))){
			echo str_repeat(" ",512);
		}
		echo 'this 404 page <br />';
		echo $message;
		exit;
	}

	/**
	 * 创建目录
	 * @param $dir 目录路径
	 * @return boolean true/false;
	 */
	public static function createDir($dir)
	{
		return is_dir($dir) or (self::createDir(dirname($dir)) and @mkdir($dir, 0777));
	}

	/**
	 * 读取配置
	 */
	public static function config($str)
	{
		list($main, $sub) = explode('.', $str);
		if (isset($GLOBALS['config'][$main]) && isset($GLOBALS['config'][$main][$sub])) return $GLOBALS['config'][$main][$sub];
		if (isset($GLOBALS['config'][$main])) return $GLOBALS['config'][$main];
		return $GLOBALS['config'];
	}

	/**
	 * 重定向
	 *
	 * @param  mixed   string site URI or URL to redirect to, or array of strings if method is 300
	 * @param  string  HTTP method of redirect
	 * @param  object swoole_http_response
	 * @return void
	 */
	public static function redirect($uri = '', $method = '302', $response = '')
	{
		$codes = array
		(
				'refresh' => 'Refresh',
				'301' => 'Moved Permanently',
				'302' => 'Found',
				'303' => 'See Other',
				'304' => 'Not Modified',
				'305' => 'Use Proxy',
				'307' => 'Temporary Redirect'
		);
		// Validate the method and default to 302
		$method = isset($codes[$method]) ? (string) $method : '302';

		if (strpos($uri, '://') === FALSE){
			$uri = input::uri('base').$uri;
		}
		if(USE_SWOOLE && $response instanceof swoole_http_response) {
			if ($method === 'refresh') {
				$response->header('Refresh', '0; url=' . $uri);
			} else {
				$response->status(302);
				$response->header('Location', $uri);
			}
			//$response->end('<h1>' . $method . ' - ' . $codes[$method] . '</h1>');
		}else {
			if ($method === 'refresh') {
				header('Refresh: 0; url=' . $uri);
			} else {
				header('HTTP/1.1 ' . $method . ' ' . $codes[$method]);
				header('Location: ' . $uri);
			}
			//echo '<h1>' . $method . ' - ' . $codes[$method] . '</h1>';
		}
		return true;
	}

	/**
	 * 请求URL-PHP程序接口
	 * @param string:$url: 请求接口地址
	 * @param $params:string/array 参数
	 * @param $post: bool:是否使用post提交
	 * @param $headers: array:请求头信息
	 * @return string 请求结果
	 */
	public static function curlExec($url, $params, $post = false, $headers = array()){
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://'){
			curl_setopt( $ch, CURLOPT_URL, $url );
		}else {
			curl_setopt( $ch, CURLOPT_URL, input::uri('base').$url );
		}
		if ($post) curl_setopt( $ch, CURLOPT_POST, 1 );
		if (is_string($params)){
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
		}else {
			$tempArr = array();
			foreach ($params as $k=>$v){
				$tempArr[] = $k.'='.urlencode($v);
			}
			curl_setopt( $ch, CURLOPT_POSTFIELDS, join('&', $tempArr) );
		}
		if(!empty($headers)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		return $contents;
	}
}
