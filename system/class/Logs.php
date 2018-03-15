<?php

/**
 * 
 * 日志记录类 - 写文件记录
 * @author zsf
 * @version v1.0
 * @souce 来源网络
 * 
 */

class Logs{
	//文件保存路径
    private $FilePath; 
    
    //日志保存文件名称
    private $FileName;
    
    private $m_MaxLogFileNum;
    private $m_RotaType;
    private $m_RotaParam;
    private $m_InitOk;
    private $m_Priority;
    private $m_LogCount;
    
    const EMERG  = 0;
    const FATAL  = 0;
    const ALERT  = 100;
    const CRIT   = 200;
    const ERROR  = 300;
    const WARN   = 400;
    const NOTICE = 500;
    const INFO   = 600;
    const DEBUG  = 700;

    /**
     * @abstract 初始化
     * @param String $dir 文件路径
     * @param String $filename 文件名
     * @param String $priority 权限码
     * @param int $maxlogfilenum 最大日志文件数量
     * @param int $rotatype 轮训类型
     * @param int $rotaparam 日志记录最大数量
     * @return 
     */
    public function Logs($dir, $filename, $priority = Logs::INFO, $maxlogfilenum = 3, $rotatype = 1, $rotaparam = 5000000)
    {
        $dot_offset = strpos($filename, ".");
        if ($dot_offset !== false)
            $this->FileName = substr($filename,0, $dot_offset);
        else
            $this->FileName = $filename;
        $this->FilePath = $dir;
        $this->m_MaxLogFileNum = intval($maxlogfilenum);
        $this->m_RotaParam = intval($rotaparam);
        $this->m_RotaType = intval($rotatype);
        $this->m_Priority = intval($priority);
        $this->m_LogCount = 0;

        $this->m_InitOk = $this->InitDir();
        umask(0000); 
        $path=$this->createPath($this->FilePath,$this->FileName);
        if(!$this->isExist($path))
        {
            if(!$this->createDir($this->FilePath))
            {
                #echo("创建目录失败!");
            }
            //if(!$this->createLogFile($path)){
                #echo("创建文件失败!");
           //}
        }
    }
    
    /**
     * @abstract 创建路径
     * @param String $log 内容
     */
    private function createPath($filepath, $filename)
    {
        return $filepath.$filename;
    }
    
	private function InitDir()
    {
        if (is_dir($this->FilePath) === false)
        {
            if(!$this->createDir($this->FilePath))
            {
                //echo("创建目录失败!");
                //throw exception
                return false;
            }
        }
        return true;
    }
    
    /**
     * @abstract 写入日志
     * @param String $log 内容
     */
    
    function setLog($log)
    {
        $this->Log(Logs::NOTICE, $log);
    }
    function LogDebug($log)
    {
        $this->Log(Logs::DEBUG, $log);
    }
    function LogError($log)
    {
        $this->Log(Logs::ERROR, $log);
    }
    function LogNotice($log)
    {
        $this->Log(Logs::NOTICE, $log);
    }
	function LogInfo($log)
    {
        $this->Log(Logs::INFO, $log);
    }
    function Log($priority, $logContent)
    {
        if ($this->m_InitOk == false)
            return;
        if ($priority > $this->m_Priority)
            return;
        $path = $this->getLogFilePath($this->FilePath, $this->FileName).".log";
        $handle=@fopen($path,"a+");
        if ($handle === false)
        {
            return;
        }
        /** OLD CODES START **/
        //$datestr = strftime("%Y-%m-%d %H:%M:%S ");
        //$caller_info = $this->get_caller_info();
        //var_dump($caller_info);
        //if(!@fwrite($handle, $caller_info.$datestr.$log."\n")){//写日志失败
            //echo("写入日志失败");
        //}
        /** OLD CODES END **/
    	if(!@fwrite($handle, $logContent."\n")){
            echo("写入日志失败");
        }
        @fclose($handle);
        $this->RotaLog();
    }
    private function get_caller_info()
    {
    	$ret = debug_backtrace();
    	foreach ($ret as $item)
    	{
    		if(isset($item['class']) && 'Logs' == $item['class'])
    		{
    			continue;
    		}
    		$file_name = basename(@$item['file']);
    		return @<<<S
{$file_name}:{$item['line']}  
S;

    	}
    }
    private function RotaLog()
    {
        $file_path = $this->getLogFilePath($this->FilePath, $this->FileName).".log";
        if ($this->m_LogCount%10==0)
            clearstatcache();
        ++$this->m_LogCount;
        $file_stat_info = stat($file_path);
        if ($file_stat_info === FALSE)
            return;
        if ($this->m_RotaType != 1)
            return;
     
        //echo "file: ".$file_path." vs ".$this->m_RotaParam."\n";
        if ($file_stat_info['size'] < $this->m_RotaParam)
            return;

        $raw_file_path = $this->getLogFilePath($this->FilePath, $this->FileName);
        $file_path = $raw_file_path.($this->m_MaxLogFileNum - 1).".log";
        //echo "lastest file:".$file_path."\n";
        if ($this->isExist($file_path))
        {
            unlink($file_path);
        }
        for ($i = $this->m_MaxLogFileNum - 2; $i >= 0; $i--)
        {
            if ($i == 0)
                $file_path = $raw_file_path.".log";
            else
                $file_path = $raw_file_path.$i.".log";

            if ($this->isExist($file_path))
            {
                $new_file_path = $raw_file_path.($i+1).".log";
                if (rename($file_path, $new_file_path) < 0)
                {
                    continue;
                }
            }
        }
    }

    function isExist($path){
        return file_exists($path);
    }

    /**
     * @abstract 创建目录
     * @param <type> $dir 目录名
     * @return bool
     */
    function createDir($dir){
        return is_dir($dir) or ($this->createDir(dirname($dir)) and @mkdir($dir, 0777));
    }

    /**
     * @abstract 创建日志文件
     * @param String $path
     * @return bool
     */
    function createLogFile($path){
        $handle=@fopen($path,"w"); //创建文件
        @fclose($handle);
        return $this->isExist($path);
    }

    /**
     * @abstract 创建路径
     * @param String $dir 目录名
     * @param String $filename 
     */
    function getLogFilePath($dir,$filename){
        return $dir."/".$filename;
    }
	
	/**
	 * 读取日志文件内容
	 * @param String $filepath  文件完整路径
	 * @param number $num 日志读取条数，  时间从新到旧
	 * @param number $length 每行的最大长度值，单位：字节； 设置了可以让程序更高效，默认1024
	 * @author zhengshufa
     * @return MAX bool/string
	 */
	static function readLogs($filepath, $num = 10, $length = 1024) {
		if (!file_exists($filepath)) return false;
		//打开文件，取得$handle
		$handle = @fopen ( $filepath, "r" );
		$readnum = 0;
		$content = '';
		
		if ($handle) {
			while ( ($buffer = fgets ( $handle, $length )) !== false ) {
				$content .= $buffer;
				$readnum++;
				if($readnum == $num){ break;}
			}
			//if (! feof ( $handle )) {
				//echo "Error: unexpected fgets() fail\n";
			//}
			fclose ( $handle );
			return $content;
		}else{
			return false;
		}
	}
    
}

/**使用实例
$logPath = DOCROOT.'logs/browseRecords/';
$fileName = 'log'.date('Y-m-d');
$logs = new Logs($logPath, $fileName);
$logContent = array('log_time'=>date('Y-m-d H:i:s'), 'ip'=>common::getIp(),'date'=>date('Y-m-d'));
$logContent = json_encode($logContent);
$logs->LogInfo($logContent);

**/
?>