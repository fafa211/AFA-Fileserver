<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * 微服务 父控制类, 全部微服务需继承此基类
 * @author zhengshufa
 * @date 2016-08-02 15:20:55
 */
class Server_Controller extends Controller
{

    /**
     * @var array 返回结果数组格式
     * errNum = 0 为成功, 非0为失败
     * errMsg 提示信息
     * retData  返回的结果数组
     */
    protected $ret = array('errNum'=>0, 'errMsg'=>'success', 'retData'=>array());

    /**
     * @var int $account_id 授权帐号ID, 通过key或token解密获得
     */
    protected $account_id = 0;

    /**
     * @var array 允许使用token进行授权访问的方法
     */
    protected $use_token = array();

    /**
     * @var string $token 临时授权码, 可通过getoken接口获得
     */
    protected $token = '';

    /**
     * @var int $token_time token生成时间戳
     */
    protected $token_time = 0;

    /**
     * @var int $token_valid_time token有效时间,默认600秒
     */
    protected $token_valid_time = 600;

    /**
     * @var int 当前时间戳
     */
    protected $current_time = 0;

    /**
     * @var string 客户IP
     */
    protected $client_ip = '';

    /**
     * @var string 请求访问唯一ID, 由系统自动生成, 每次请求都不一样
     */
    protected $uniqid = '';

    /**
     * @var string 记录服务调用日志文件保存路径
     */
    public static $log_path = '';//

    /**
     * @var array 不需要登录监测的方法
     */
    protected $_nologin_action = array();

    /**
     * @var 为true,则表示无需授权验证的controller,
     */
    protected $_nologin_controller = false;

    /**
     * 执行前的准备与日志记录
     * @return boolean
     */
    public function before(){

        //忽略man方法
        if('man' == $this->request->method)  return true;

        //验证是否通过标识
        $verify = false;
        //访问控制key
        $keyno = trim(input::get('key'));
        //初始化当前时间戳
        $this->current_time = time();

        if(!empty($keyno)) {
            //账户key授权验证
            $keystr = F::authstr($keyno, 'DECODE');
            $keyarr = explode(',', $keystr);

            if (isset($keyarr[0]) && isset($keyarr[1]) && is_numeric($keyarr[0]) && $keyarr[0]) {
                $this->account_id = $keyarr[0];
                $verify = true;
            }
        }elseif($token = trim(input::get('token'))){
            //token授权验证
            if(is_array($this->use_token) && in_array($this->request->method, $this->use_token)) {
                //解密token
                $str = F::authstr($token, 'DECODE');
                if (!empty($str)) {
                    list($this->account_id, $time) = explode(',', $str);

                    if ($this->account_id && ($time + $this->token_valid_time) > $this->current_time) {
                        $this->token = $token;
                        $this->token_time = $time;

                        $verify = true;
                    }
                }
            }
        }else{
            //无需授权验证的controller   &  无需授权验证的action
            if(true === $this->_nologin_controller || in_array($this->request->method, $this->_nologin_action)) {
                $verify = true;
            }
        }

        //客户端IP地址
        $this->client_ip = common::getIp();
        //接口调用唯一标识ID
        $this->uniqid =  md5(uniqid().$this->client_ip);
        self::$log_path = PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'serverlog'.DIRECTORY_SEPARATOR;


        $logs = new Logs(self::$log_path, date('Y-m-d') . '.log');
        $logContent = array(
            'id' => $this->uniqid,
            'log_time' => date('Y-m-d H:i:s'),
            'acccount_id'=> $this->account_id,
            'ip' => $this->client_ip,
            'api' => $this->request->controller.'/'.$this->request->method,//接口名称
            'params' => $this->request->params,//接口参数
            'get_params' => input::get(),
            'post_parms' => input::post()
        );
        $logContent = F::json_encode($logContent);
        $logs->LogInfo($logContent);

        if($verify === false){
            $this->ret['errNum'] = "-1";
            $this->ret['errMsg'] = "token验证失败或已失效!";

            $this->echojson($this->ret);
        }

        return $verify;

    }

    /**
     * 执行后的服务调用记录,调用前后可通过唯一ID($this->uniqid)识别
     */
    public function after(){

        //忽略man方法
        if('man' == $this->request->method)  return;

        $logs = new Logs(self::$log_path, date('Y-m-d') . '.log');

        $logContent = array(
            'id'=>$this->uniqid,
            'log_time'=>date('Y-m-d H:i:s'),
            'acccount_id'=> $this->account_id,
            'return'=>$this->ret
        );
        $logContent = F::json_encode($logContent);
        $logs->LogInfo($logContent);

    }


    /**
     * 取得回话token值
     */
    public function getToken_Action(){

        $time = time();
        $this->ret['retData'] = F::authstr($this->account_id.','.$time, 'ENCODE');

        return $this->echojson($this->ret);

    }


}
