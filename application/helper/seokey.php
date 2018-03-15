<?php
class seokey{

// 获取关键词及所属来源搜索引擎名称


//需要分析的搜索引擎
public static $config = array (
	"s1" => array ("domain" => "baidu.com", "kw" => "wd", "charset" => "gbk" ), 
	"s11" => array ("domain" => "baidu.com", "kw" => "word", "charset" => "gbk" ),
    "s13" => array ("domain" => "baidu.com", "kw" => "word", "charset" => "utf-8" ),  
	"s2" => array ("domain" => "google.com", "kw" => "q", "charset" => "utf-8" ), 
	"s3" => array ("domain" => "google.com.hk", "kw" => "q", "charset" => "utf-8" ), 
	"s4" => array ("domain" => "bing.com", "kw" => "q", "charset" => "utf-8" ), 
	"s5" => array ("domain" => "soso.com", "kw" => "w", "charset" => "gbk" ), 
	"s6" => array ("domain" => "sogou.com", "kw" => "query", "charset" => "gbk" ), 
	"s7" => array ("domain" => "youdao.com", "kw" => "q", "charset" => "utf-8" ), 
	"s8" => array ("domain" => "yahoo.cn", "kw" => "q", "charset" => "utf-8" ), 
	"s9" => array ("domain" => "qihoo.com", "kw" => "kw", "charset" => "gbk" ),
    "s10" => array ("domain" => "360.cn", "kw" => "q", "charset" => "gbk" ),
    "s12" => array ("domain" => "so.com", "kw" => "q", "charset" => "utf-8" ),
    "s14" => array ("domain" => "haosou.com", "kw" => "q", "charset" => "utf-8" ),
    "s15" => array ("domain" => "m.sogou.com", "kw" => "keyword", "charset" => "utf-8" ), 
);

// 函数作用：从url中提取关键词。参数说明：url及关键词前的字符。
public static function get_keyword($url, $kw_start) {
	if (preg_match ( "/\?{$kw_start}/", $url ) > 0) {
		$kw_start = "?" . $kw_start;
	} elseif (preg_match ( "/&{$kw_start}/", $url ) > 0) {
		$kw_start = "&" . $kw_start;
	}
	$start = stripos ( $url, $kw_start );
	if ($start === false) return '';
	$url = substr ( $url, $start + strlen ( $kw_start ) );
	$start = stripos ( $url, '&' );
	if ($start > 0) {
		$start = stripos ( $url, '&' );
		$s_s_keyword = substr ( $url, 0, $start );
	} else {
		$s_s_keyword = substr ( $url, 0 );
	}
	return $s_s_keyword;
}

//分析地址
public static function anlylize($url, $decode = FALSE){

	$arr_key = array ();
	$F_Skey = '';
	foreach ( self::$config as $item ) {
		$sh = preg_match ( "/\b{$item['domain']}\b/", $url );
		if ($sh) {
			$query = $item ['kw'] . "=";
			
			$s_s_keyword = self::get_keyword ( $url, $query );
			if ($decode) {
				$F_Skey = urldecode ( $s_s_keyword );
			}else {
				$F_Skey = $s_s_keyword;
			}
			if (! self::is_utf8 ( $F_Skey )){
				$F_Skey = @iconv ( "gbk", "UTF-8", $F_Skey ); // 最终提取的关键词
			}
			if($item['domain'] == 'baidu.com'){//匹配 " www.baidu.com/s?word=关键词 "    这种方式的
			    if(empty($F_Skey)) continue;
				$sh2 = preg_match ( "/\b{$item['domain']}\b/", $F_Skey );
				if ($sh2){
					$query = "word=";
					$s_s_keyword = self::get_keyword ( $F_Skey, $query );
					$F_Skey = $s_s_keyword;
					if (! self::is_utf8 ( $F_Skey )) {
						$F_Skey = iconv ( "gbk", "UTF-8", $F_Skey ); // 最终提取的关键词
					}
				}
			}
			
			//$keys = explode ( " ", $F_Skey );
			//$arr_key [$item ['domain']] = $keys;
			if($F_Skey){
			   break;
			}
		}
	}
	return ( $F_Skey );

}
//判断是否为UTF8编码
public static function is_utf8($word) {
	if (preg_match ( "/^([" . chr ( 228 ) . "-" . chr ( 233 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}){1}/", $word ) == true 
	 || preg_match ( "/([" . chr ( 228 ) . "-" . chr ( 233 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}){1}$/", $word ) == true 
	 || preg_match ( "/([" . chr ( 228 ) . "-" . chr ( 233 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}[" . chr ( 128 ) . "-" . chr ( 191 ) . "]{1}){2,}/", $word ) == true) 
	{
		return true;
	} else {
		return false;
	}
}
}