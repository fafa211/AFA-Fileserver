<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * Admin 微服务管理类, 管理员管理基础控制器
 * @author zhengshufa
 * @date 2016-09-13 15:20:55
 */
class Admin_Controller extends Controller
{
    /**
     * @var array 返回结果数组格式
     * errNum = 0 为成功, 非0为失败
     * errMsg 提示信息
     * retData  返回的结果数组
     */
    protected $ret = array('errno'=>1, 'err'=>'success', 'rsm'=>array());

    /**
     * @var array css 文件载入
     */
    protected $_import_css_files = array(G_STATIC_URL."/admin/css/common.css");

    /**
     * @var array js 文件载入
     */
    protected $_import_js_files = array(
        G_STATIC_URL."/js/jquery.2.js",
        G_STATIC_URL."/admin/js/aws_admin.js",
        G_STATIC_URL."/admin/js/aws_admin_template.js",
        G_STATIC_URL."/js/jquery.form.js",
        G_STATIC_URL."/admin/js/framework.js",
        G_STATIC_URL."/admin/js/global.js"
    );

    /**
     * @var array 不需要登录监测的方法
     */
    protected $_nologin_action = array();

    /**
     * 执行前的准备与日志记录
     * @return boolean
     */
    public function before(){

        if($this->request->method == 'login' || $this->request->method == 'logout' || $this->request->method == 'login_process') return true;

        if(in_array($this->request->method, $this->_nologin_action)) return true;

        if(!Auth::instance()->logged_in()){
            common::redirect(input::uri('base').'admin/login');
        };

//        $sysaccount = new Sysaccount_auth_Service('sysuser');
//
//        if(!$sysaccount->hasPermission($this->request->url)){
//            $this->ret['err'] = 'has no permission';
//            $this->echojson($this->ret);
//            return false;
//        }

    }

    /**
     *  构造函数
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        View::$global_params['_import_css_files'] = &$this->_import_css_files;
        View::$global_params['_import_js_files'] = &$this->_import_js_files;
        $user = Auth::instance()->get_user();
        if($user){
            $this->view->user = Auth::instance()->get_user();
        }

        //$sysaccount = new Sysaccount_auth_Service('sysuser');
        //$menu_list = $sysaccount->findMenus($request->url);
        $menu_list = array();

        $this->view->menu_list = $menu_list;
        $this->view->page_title = "管理后台";
    }

    /**
     * 登录
     */
    public function login_Action(){

        $view = &$this->view;
        $view->page_title = "管理员登陆";
        $view->user_id = 0;

        $this->_import_css_files[] = G_STATIC_URL."/admin/css/login.css";


        $view->set_view(APPPATH.'view/admin/login');
        $view->render();

    }

    /**
     * Ajax 登录
     */
    public function login_process_Action(){

        if (input::post()) {

            if(!Captcha::valid(input::post('seccode_verify'))){
                $this->ret['errno'] = 100;
                $this->ret['err'] = "验证码不正确.";
                return $this->echojson($this->ret);
            };

            $account = input::post('account');
            $passwd = input::post('passwd');

            if(Auth::instance()->login($account, $passwd)){
                $this->ret['rsm']['url'] = input::uri('base').'admin/index';
                $this->echojson($this->ret);
            }else{
                $this->ret['errno'] = 101;
                $this->ret['err'] = "登录失败,帐号或密码不正确.";
                $this->echojson($this->ret);
            }
            return;
        }
    }

    /**
     * 退出
     */
    public function logout_Action(){
        Auth::instance()->logout(true, true);
        common::redirect(input::uri('base').'admin/login', 302, $this->request->response);
    }

    /**
     * 退出
     */
    public function index_Action(){
        $view = &$this->view;
        $view->set_view(APPPATH.'view/admin/index');
        $view->render();
    }

    /**
     * 左边菜单
     */
    public function left_Action(){
        $view = &$this->view;
        $view->set_view(APPPATH.'view/admin/left');
        $view->domain = input::uri('base');
        $view->render();
    }

    /**
     * 头部
     */
    public function head_Action(){
        $view = &$this->view;
        $view->set_view(APPPATH.'view/admin/head');
        $view->domain = input::uri('base');
        $view->render();
    }

    /**
     * 退出
     */
    public function home_Action(){
        $view = &$this->view;
        $view->set_view(APPPATH.'view/admin/home');
        $view->render();
    }



}
