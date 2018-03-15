<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * article模型控制器
 * @author zhengshufa
 * @date 2017-04-05 13:35:39
 */
class Spage_Controller extends Admin_Controller
{

    private $vdir = '';

    public function __call($method = '', $params = '')
    {
        //$result = Page_api_Service::get($this->request->method);

        //print_r($result);

        Request::instance('page/fpage/show/'.$this->request->method)->run();
    }

}
