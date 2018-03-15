<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * man 手册模型控制器
 * @author zhengshufa
 * @date 2016-08-02 15:20:55
 */
class Man_Controller extends Controller
{

    private $vdir = '';

    /**
     * @param $class_name 类名称
     * @param string $method 方法名称
     */
    public function sys_Action($class_name, $method = ''){

        $view = &$this->view;
        $view->set_view($this->vdir . 'sys');


        $view->methodArr = docparse::methodParse($class_name, $method);
        $view->class = $class_name;
        $view->render();

    }

    /**
     * @param $class_name 类名称
     * @param string $method 方法名称
     */
    public function api_Action($class_name, $method = ''){

        //$contr = $class_name.'_Controller';
        $file = F::find_file('controller', $class_name, $class_name);
        if(file_exists($file)) include_once $file;

        $view = &$this->view;
        $view->set_view($this->vdir . 'api');


        $view->methodArr = docparse::methodParse($class_name.'_Controller', $method);
        $view->class = $class_name;
        $view->method = $method;
        $view->render();

    }


    /**
     * 列表管理 man
     */
    public function lists_Action()
    {
        //类列表
        $classArr = array(
            'amchart', 'Arr', 'Auth', 'Cache', 'Captcha',
            'common', 'Cookie', 'Curl', 'db', 'Email',
            'Encrypt','F', 'Form', 'HTML', 'Image',
            'Logs', 'Page', 'Security', 'Session', 'Text',
            'Upload', 'Valid', 'Validation', 'sql',
            'client', 'docparse', 'funs', 'seokey'
        );

        $view = &$this->view;
        $view->set_view($this->vdir . 'lists');
        $view->classArr = $classArr;
        $view->render();
    }

    /**
     * 手册主页
     */
    public function index_Action()
    {
        $view = &$this->view;
        $view->set_view($this->vdir . 'index');
        $view->render();
    }


}
