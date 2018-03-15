<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * Kingsum 模型控制器
 * @author zhengshufa
 * @date 2017-06-19 13:35:39
 */
class Kingsum_Controller extends Controller
{

    /**
     * @var string $vdir 视图文件目录
     */
    private $vdir = APPPATH.'view'.DIRECTORY_SEPARATOR.'kingsum'.DIRECTORY_SEPARATOR;

    /**
     * @var array css 文件载入
     */
    protected $_import_css_files = array();

    /**
     * @var array js 文件载入
     */
    protected $_import_js_files = array();

    /**
     * @var object meta setting
     */
    protected $hmeta = array('title' => '神州金山-发展智慧消防物联网,筑起城市防火墙', 'keywords' => '神州金山,消防物联网,智慧消防,神州金安,神州天眼,消防大数据,云平台',
        'description'=>'发展智慧消防物联网,筑起城市防火墙, 神州金山物联网科技有限公司，始终致力于消防行业物联网+互联网解决方案的研制与开发，在业内率先推出多款引领行业趋势的新产品，掌握了一系列核心技术，积累了丰富的运行经验，形成了门类齐全的消防行业物联网+互联网系列产品线，并多次获得公安部消防局和江苏省公安厅的科学技术类奖项。');

    /**
     * @var array 返回结果数组格式
     * errNum = 0 为成功, 非0为失败
     * errMsg 提示信息
     * retData  返回的结果数组
     */
    protected $ret = array('errNum'=>0, 'errMsg'=>'success', 'retData'=>array());

    /**
     *  构造函数
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        View::$global_params['_import_css_files'] = &$this->_import_css_files;
        View::$global_params['_import_js_files'] = &$this->_import_js_files;
        View::$global_params['_global_url'] = '/kingsum/';
        $this->hmeta = (object) $this->hmeta;
        View::$global_params['hmeta'] = & $this->hmeta;
    }

    /**
     * 首页
     */
    public function index_Action(){
        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/index.css');
        array_push($this->_import_js_files, G_STATIC_URL.'/kingsum/js/Carousel.js');

        $view = new View($this->vdir.'index.html');

        $view->render(true);
    }

    /**
     *
     * 产品页
     */
    public function product_Action($flag = 'jinan'){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/product_cen_detail.css');

        $template = array('jinan'=>'productcendetail.html', 'tianyan'=>'productcendetail_2.html', 'zhenggong'=>'productcendetail_3.html');
        if(!isset($template[$flag])) {
            return common::jsTip("你访问的页面不存在!",'/');
        }

        $titleArr = array('jinan'=>'神州金安', 'tianyan'=>'神州天眼', 'zhenggong'=>'神州政工');
        $this->hmeta->title = $titleArr[$flag].'-产品中心-神州金山';

        $view = new View($this->vdir.$template[$flag]);

        $view->render(true);
    }

    /**
     *
     * 解决方案
     */
    public function solution_Action($flag = 'school'){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/solution.css');
        $this->hmeta->title = "解决方案-神州金山";

        $template = array('school'=>'solution_4.html', 'common'=>'solution_5.html', 'gov'=>'solution.html', 'hospital'=>'solution_2.html', 'factory'=>'solution_3.html');
        if(!isset($template[$flag])) {
            return common::jsTip("你访问的页面不存在!",'/');
        }

        $view = new View($this->vdir.$template[$flag]);

        $view->render(true);
    }

   /**
     *
     * 经典案例
     */
    public function customercase_Action($flag = 'wanda'){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/customercase.css');
        $this->hmeta->title = "客户案例-神州金山";

        $template = array('wanda'=>'customercase_4.html', 'langfang'=>'customercase_2.html', 'shanghaidianli'=>'customercase_3.html', 'boshi'=>'customercase.html');
        if(!isset($template[$flag])) {
            return common::jsTip("你访问的页面不存在!",'/');
        }

        $view = new View($this->vdir.$template[$flag]);

        $view->render(true);
    }

    /**
     *
     * 企业介绍/历程/加入我们
     */
    public function company_Action($flag = 'aboutus'){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/aboutus.css');

        $template = array('aboutus'=>'aboutus.html', 'processing'=>'aboutus_2.html', 'rongyu'=>'aboutus_4.html', 'joinus'=>'aboutus_5.html');
        if(!isset($template[$flag])) {
            return common::jsTip("你访问的页面不存在!",'/');
        }
        $titleArr = array('aboutus'=>'关于我们-关于神州金山', 'processing'=>'神州金山大事记', 'rongyu'=>'公司荣誉', 'joinus'=>'联系我们');
        $this->hmeta->title = $titleArr[$flag].'-神州金山';

        $view = new View($this->vdir.$template[$flag]);

        $view->render(true);
    }

    /**
     *
     * 资讯中心
     */
    public function newscenter_Action(){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/aboutus.css');
        $this->hmeta->title = '资讯中心-神州金山';

        $view = new View($this->vdir.'aboutus_3.html');

        //读取文章列表与分页
        $total = Article_api_Service::countAritcle();
        $page = Page::instance($total, 10);

        $view->pagination = $page->modeTwo();
        $view->lists = Article_api_Service::searchAritcle(array(), $page->limit);

        $view->render(true);
    }

    /**
     *
     * 资讯中心-查看资讯内容
     */
    public function newsshow_Action($id){

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/aboutus.css');

        $view = new View($this->vdir.'aboutus_newdetails.html');

        //读取文章内容
        $view->article = Article_api_Service::read($id);
        $view->nextobj = (object)Article_api_Service::next($id);
        $view->prevobj = (object)Article_api_Service::prev($id);

        $this->hmeta->title = $view->article->title.'-神州金山';
        $this->hmeta->description = F::substr(trim(strip_tags($view->article->message)), 0, 200);

        $view->render(true);
    }

    /**
     *
     * 留言框
     */
    public function leaveword_Action(){

        if(!empty(input::post())){
            $leaveword = Leaveword_api_Service::add();
            if($leaveword === false){
                $this->ret['errNum'] = 1;
                $this->ret['errMsg'] = "提交失败,请确认真实姓名与联系手机有效.";
            }else{
                $this->ret['retData'] = $leaveword;
            }

            return $this->echojson($this->ret);
        }

        array_push($this->_import_css_files, G_STATIC_URL.'/kingsum/css/leavemessage.css');

        $view = new View($this->vdir.'leavemessage.html');


        $view->render(true);
    }

}
