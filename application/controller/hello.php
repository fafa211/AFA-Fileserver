<?php
/**
 * 框架使用实例
 * @author zhengshufa
 * 
 *
 */

class Hello_Controller extends Controller{
    
    /**
     * hello world
     */
    public function index_Action(){

        //$result = common::curlExec('http://api.service.com/iparea/get/116.231.59.67');
        //$result = json_decode($result);
        //print_r($result);
        //echo 'Hello World!';
//        phpinfo();
        $str = 'f01b4VFQBBQZUA1UDVVJUA1VUWlRVV1cHA10EAFsDCAIEV1oGVgADVVUFXQZRUl0HVlcFAw0DB1xQCQBVAA';
        echo F::authstr($str, 'DECODE');
        echo strlen($str);

        echo '<br/>'.time();
        echo '<br />';

        $auth =  Auth::instance();

        //echo $auth->login('admin', 'admin123', true);
        echo '<br />';

        $str = array('name'=>'郑书发', 'sex'=>"男", 'birthday'=>'2000-12-08', 'like'=>'music');
        echo $ret = F::json_encode($str);
        //print_r(json_decode($ret));
        echo '<br />';
        echo date('Y-m-d H:i:s', 1492272000);
        //print_r(input::session());

    }
    
    /**
     * view test
     */
    public function view_Action(){

        $view = & $this->view;
        $view->set_view('view');

        //$view->name = 'it is view function';
        $view->name = 'GIT Test!';
        $view->value = 'Github update!';

        $api_domain = input::uri('base');//'http://api.afaphp.com/';//input::uri('base')
        $api_key    = '92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI';//'4c35bVgFUB1YIVVMBA1ZUBQwMWFcMClFVCQYNAlBWG1BVDggI';//92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI

//var_dump(input::file());
        //return;
        $file_arr = input::file();
        if(!empty($file_arr)) {
            $server_url = $api_domain . 'fileServer/upload?key='.$api_key;
            $file = input::file('myFile');

            if ($file['error'] === 0) {

                $params = array(
                    'file' => '@'.$file['tmp_name'],
                    'type'=>$file['type'],
                    'name'=>$file['name'],
                    //'size'=>json_encode(array(array(600,600), array(400,400), array(200,200)))
                );

                $config = array(CURLOPT_BINARYTRANSFER => true);
                $result = Curl::post($server_url, $params, array(), false, $config);

                var_dump( $result );

            }
        }

        $token_url = $api_domain . 'fileServer/getToken?key='.$api_key;

        $result = Curl::get($token_url);
        $result = json_decode($result);

        //echo $result->retData."\n";

        $view->token = $result->retData;
        $view->api_domain = $api_domain;
        $view->hash_id = '2fbf5U1ZTVlVTB1FWVQBbAQUOVgcBUg8CV1IBVQRXVQYHAAcGBVUFVgEOBwNdUQIGUFIGUVhRUQUHDg5TUg';
        $view->hash_id_image = '5d56bVQJTAggHUgMFVlIAWFAGC1UGBgNQAFRaXwZSCAQEBgBXVFoCCgEEAwJUAQIJUlJaUAsOB15aVwYPVg';


        //$delete_url = input::uri('base') . 'fileServer/delete/3c8a6BAlUBwBWBQECAAZVAApTAVBUAVNVDwMCV1sIU18BVwUNX1RSVgAGBwQJUFsHAgdUAVtRUVJQVVRRAg?key='.$api_key;
        //$result = Curl::get($delete_url);
        //$result = json_decode($result);
        //print_r($result);
        //echo F::authstr('3c8a6BAlUBwBWBQECAAZVAApTAVBUAVNVDwMCV1sIU18BVwUNX1RSVgAGBwQJUFsHAgdUAVtRUVJQVVRRAg','DECODE');


        $view->render(true);
        
        //$this->template->content = $view;
        
        //$this->template->render();
    }

    /**
     * view test
     */
    public function view2_Action(){
        $view = new View('view2');
        //$view->name = 'it is view function';
        $view->name = 'GIT Test!';
        $view->value = 'Github update!';

        $api_domain = 'http://api.afaphp.com/';//input::uri('base')
        $api_key    = '4c35bVgFUB1YIVVMBA1ZUBQwMWFcMClFVCQYNAlBWG1BVDggI';//92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI


        $data = file_get_contents("php://input");

        print_r($data);

        $file_arr = input::file();
        if(!empty($file_arr)) {
            $server_url = $api_domain . 'fileServer/upload?key='.$api_key;
            //$file = input::file('myFile');




            if ($file['error'] === 0) {

                $params = array(
                    'file' => '@'.$file['tmp_name'],
                    'type'=>$file['type'],
                    'name'=>$file['name'],
                    //'size'=>json_encode(array(array(600,600), array(400,400), array(200,200)))
                );

                $config = array(CURLOPT_BINARYTRANSFER => true);
                $result = Curl::post($server_url, $params, array(), false, $config);

                echo $result;

            }
        }

        $token_url = $api_domain . 'fileServer/getToken?key='.$api_key;

        $result = Curl::get($token_url);
        $result = json_decode($result);

        echo $result->retData;

        $view->token = $result->retData;
        $view->api_domain = $api_domain;
        $view->hash_id = '2fbf5U1ZTVlVTB1FWVQBbAQUOVgcBUg8CV1IBVQRXVQYHAAcGBVUFVgEOBwNdUQIGUFIGUVhRUQUHDg5TUg';
        $view->hash_id_image = '5d56bVQJTAggHUgMFVlIAWFAGC1UGBgNQAFRaXwZSCAQEBgBXVFoCCgEEAwJUAQIJUlJaUAsOB15aVwYPVg';


        //$delete_url = input::uri('base') . 'fileServer/delete/3c8a6BAlUBwBWBQECAAZVAApTAVBUAVNVDwMCV1sIU18BVwUNX1RSVgAGBwQJUFsHAgdUAVtRUVJQVVRRAg?key='.$api_key;
        //$result = Curl::get($delete_url);
        //$result = json_decode($result);
        //print_r($result);
        //echo F::authstr('3c8a6BAlUBwBWBQECAAZVAApTAVBUAVNVDwMCV1sIU18BVwUNX1RSVgAGBwQJUFsHAgdUAVtRUVJQVVRRAg','DECODE');


        $view->render(true);

        //$this->template->content = $view;

        //$this->template->render();
    }

    /**
     * view test
     */
    public function view3_Action(){
        $view = new View('view3');

        $api_domain = input::uri('base');
        $api_key    = '92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI';


        //$data = file_get_contents("php://input");

        //print_r($data);
        $file_arr = input::file();
        if(!empty($file_arr)) {
            $server_url = $api_domain . 'fileServer/upload?key='.$api_key;
            $file = input::file('myFile');

//print_r(input::file());die;


            if ($file['error'] === 0) {

                $params = array(
                    'file' => '@'.$file['tmp_name'],
                    'type'=>$file['type'],
                    'name'=>$file['name'],
                    //'size'=>json_encode(array(array(600,600), array(400,400), array(200,200)))
                );

                $config = array(CURLOPT_BINARYTRANSFER => true);
                $result = Curl::post($server_url, $params, array(), false, $config);

                echo $result;

            }
        }

        //$token_url = $api_domain . 'fileServer/getToken?key='.$api_key;

        //$result = Curl::get($token_url);
        //$result = json_decode($result);

        //$view->token = $result->retData;
        $key = '4c35bVgFUB1YIVVMBA1ZUBQwMWFcMClFVCQYNAlBWG1BVDggI';
        $view->token = F::getToken($key);

        $view->api_domain = $api_domain;
        $view->hash_id = '2fbf5U1ZTVlVTB1FWVQBbAQUOVgcBUg8CV1IBVQRXVQYHAAcGBVUFVgEOBwNdUQIGUFIGUVhRUQUHDg5TUg';
        $view->hash_id_image = '5d56bVQJTAggHUgMFVlIAWFAGC1UGBgNQAFRaXwZSCAQEBgBXVFoCCgEEAwJUAQIJUlJaUAsOB15aVwYPVg';

        $view->render(true);

    }

    /**
     * view test
     */
    public function view6_Action(){
        $view = new View('view6');

        $key = '4c35bVgFUB1YIVVMBA1ZUBQwMWFcMClFVCQYNAlBWG1BVDggI';
        $view->token = F::getToken($key);
        $view->api_domain = input::uri('base');;
        $view->render(true);

    }

    /**
     * view test
     */
    public function view7_Action(){
        $view = new View('view7');

        $key = '4c35bVgFUB1YIVVMBA1ZUBQwMWFcMClFVCQYNAlBWG1BVDggI';
        $view->token = F::getToken($key);
        $view->api_domain = input::uri('base');;
        $view->render(true);

    }

    /**
     * view test
     */
    public function view4_Action(){
        $view = new View('view4');

        $api_domain = input::uri('base');
        $api_key    = '92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI';


        //$data = file_get_contents("php://input");

        //print_r($data);
        $file_arr = input::file();
        if(!empty($file_arr)) {
            $server_url = $api_domain . 'fileServer/upload?key='.$api_key;
            $file = input::file('myFile');



            if ($file['error'] === 0) {

                $params = array(
                    'file' => '@'.$file['tmp_name'],
                    'type'=>$file['type'],
                    'name'=>$file['name'],
                    //'size'=>json_encode(array(array(600,600), array(400,400), array(200,200)))
                );
print_r($file);die;
                $config = array(CURLOPT_BINARYTRANSFER => true);
                //$result = Curl::post($server_url, $params, array(), false, $config);

                //echo $result;


            }
        }

        $token_url = $api_domain . 'fileServer/getToken?key='.$api_key;

        $result = Curl::get($token_url);
        $result = json_decode($result);

        $view->token = $result->retData;
        $view->api_domain = $api_domain;
        //$view->hash_id = '2fbf5U1ZTVlVTB1FWVQBbAQUOVgcBUg8CV1IBVQRXVQYHAAcGBVUFVgEOBwNdUQIGUFIGUVhRUQUHDg5TUg';
        //$view->hash_id_image = '5d56bVQJTAggHUgMFVlIAWFAGC1UGBgNQAFRaXwZSCAQEBgBXVFoCCgEEAwJUAQIJUlJaUAsOB15aVwYPVg';

        $view->render(true);

    }
    
    /**
     * view test template
     */
    public function template_Action($name = ''){
        $view = new View('view');
        $view->name = $name?$name:'it is view function';
        $view->value = 'Github update!';
    
        $this->template->content = $view;
        $this->template->render();
    }
    
    /**
     * model test
     */
    public function model_Action(){
        //添加新用户
//         $user = new User_Model();
//         $user->account = 'admin';
//         $user->passwd = md5($user->account.'123456');
//         $user->regtime = date('Y-m-d H:i:s');
//         $user->save();
        $user = new User_Model(1);
        echo $user->account;
        echo '<br />';
        
        $user2 = new User_Model();
        $user2->get('admin');
        
        echo $user2->passwd;
        echo '<br />';
        
    }
    
    /**
     * sql build test
     */
    public function sql_Action(){
        
        $sql = new sql('UPDATE');
        
        $arr = array('passwd'=>md5('fafa654321'));
        echo $sql->table('user')->set($arr)->where(array('account'=>'fafa'));
        echo '<br />';
        echo sql::select('account,passwd,regtime,id', 'user', array('id'=>1))->limit(0,2)->groupby('account')->orderby('id desc');
        echo '<br />';
        echo sql::update()->table('user')->set(array('regtime'=>date('Y-m-d H:i:s')))->where(array('id'=>1));
        echo '<br />';
        echo sql::select('*', 'user');
        echo '<br />';
        echo sql::select('*', 'user as u')->in('u.id', array(1,3,'rtew'))->innerjoin('user_textinfo as ut', 'u.id = ut.user_id');
        echo '<br />';
        echo sql::select('*', 'user as u')->in('u.id', array(1))->innerjoin('user_textinfo as ut', 'u.id = ut.user_id')->limit(1)->or_c('id','=',3)->orderby('id desc');
        
        echo '<br />';
        echo sql::select('*', 'user as u')->where('id','>',1)->innerjoin('user_textinfo as ut', 'u.id = ut.user_id')->limit(1)->or_c('id','=',3)->and_c(array('id'=>1))->orderby('id desc');
        echo '<br />';
        echo sql::delete('user')->where('id','>',3)->orderby('id desc')->limit(1);
        
        
        
    }

    public function test_Action(){
        //A字符
        $str = (pack("A*", "中国"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //H字符
        $str = (pack("H*", "fffe"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //C字符
        $str = (pack("C*", "55", "56", "57"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //i字符 短整形 32位 4个字节 64位8个字节
        $str = (pack("i", "100"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //s字符 短整形 2个字节
        $str = (pack("s", "100"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //l字符 长整形 4个字节
        $str = (pack("l", "100"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //f字符 单精度浮点 4个字节
        $str = (pack("f", "100"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
        //d字符 双精度浮点 8个字节
        $str = (pack("d", "100"));
        echo $str, "=", strlen($str), "字节\n";
        $this->getAscill($str);
    }

    private function getAscill($str){
        $arr = str_split($str);
        foreach ($arr as $v) {
            echo $v, "=", ord($v), "\n";
        }
        echo "=============\r\n\r\n";
    }

    public function head_Action(){

        $view = new View('head', $this->request->response);
        //$view->name = 'it is view function';
        //$view->name = 'GIT Test!';
        //$view->value = 'Github update!';

        $view->render(true);

    }

    public function foot_Action(){

        $view = new View('foot');
        //$view->name = 'it is view function';
        //$view->name = 'GIT Test!';
        //$view->value = 'Github update!';

        $view->render(true);

    }

    public function name_Action(){

        $view = & $this->view;

        $view->set_view('test');
        $view->name = 'it is view function';
        $view->get = 'GIT Test!';
        $view->value = 'Github update!';

        $sevalue = Session::instance()->get('mysession');
        if(empty($sevalue)) {
            Session::instance()->set('mysession', 'my session test!');
            echo "session is empty\n";
        }
        $view->mysession = Session::instance()->get('mysession');

        $view->render(true);

    }

    public function test2_Action(){

        $html = curl::post('http://127.0.0.1:9501/fileServer/getToken?key=92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI');
        //$html = common::curlExec('http://127.0.0.1:9501/fileServer/getToken?key=92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI', array());
        $this->request->response->end($html);

    }

    public function test3_Action(){
        $str = json_decode('{"code":0,"data":{"country":"\u4e2d\u56fd","country_id":"CN","area":"\u534e\u5357","area_id":"800000","region":"\u5e7f\u4e1c\u7701","region_id":"440000","city":"\u5e7f\u5dde\u5e02","city_id":"440100","county":"","county_id":"-1","isp":"\u7535\u4fe1","isp_id":"100017","ip":"14.152.68.12"}}');
        print_r($str);
        phpinfo();
    }

    public function test4_Action(){
        $to = array('e2'=>'zhengsf@kingsum.com.cn', 'cc'=>array('leiwei@kingsum.com.cn','22575353@qq.com'));

        $rt = Email::send($to, 'zhengsf@kingsum.com.cn','简介:我是郑书发','您好,很久不见,我是郑书发,很高兴认识你');
        print_r($rt);
    }

    public function test5_Action(){
        $dir = APPPATH.'view'.DIRECTORY_SEPARATOR.'kingsum'.DIRECTORY_SEPARATOR.'index.html';
        $view = new View($dir);
        $view->var = "my do it";
        $view->render(true);

        echo date('Y-m-d H:i:s').'<br />';
    }


    public function v5_Action(){

        $view = new View('view5', $this->request->response);
echo 'pppppp';
        $view->render(true);

    }

    public function v6_Action(){

        Modtest\Helper\mtest::testok();

    }

    public function v7_Action(){

        echo 'ok';

    }
    public function v8_Action(){

        //funs::QRCode('http://www.baidu.com/?flag=fafa211');

        $view = &$this->view;
        $view->set_view("view8");

        $view->render(true);

    }
    public function v9_Action(){

        funs::QRCode('http://www.baidu.com/?flag=fafa211', false, "M", 6, 3, false, true);
        //echo DOCROOT.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'test.png';

//        $view = &$this->view;
//        $view->set_view("view8");
//
//        $view->render(true);

    }

    public function v99_Action(){

        //funs::QRCode('http://www.baidu.com/?flag=fafa211');

        $view = &$this->view;
        $view->set_view("view9");

        $view->render(true);

    }
}