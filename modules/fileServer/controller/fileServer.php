<?php

/**
 * Created by PhpStorm.
 * User: ZSF
 * Date: 16/8/24
 * Time: 下午5:35
 */
class FileServer_Controller extends Server_Controller{

    //需要使用token授权的方法
    protected $use_token = array('upload','uploadPic','download', 'showImage');

    /**
     * 上传文件服务
     * token  GET  授权token,通过key在后端调用服务接口取得
     * name   GET/POST  文件名称,带后缀: 二进制传输时不能为空
     * type   GET/POST  文件类型: 二进制传输时不能为空
     * size   GET/POST  文件大小: 二进制传输时不能为空
     * chunk  GET/POST  当前分片编号,从0开始
     * chunks GET/POST  分片总数
     * id     GET/POST  上传文件ID WU_FILE_0
     * fileVal  GET/POST    存储上传文件的表单字段名称, 默认值为file
     *
     * file 普通表单file类型上传 或 二进制类型文件内容上传
     *
     * @param boolean $encrypt :0或1
     * 0 为不加密, 默认为0, 为0时将会直接返回可访问的文件地址;
     * 1为加密, 不会返回可访问地址,需要经过授权, 通过指定接口才能访问;
     *
     * @return json {
     * "errNum":0,          //0标识成功
     * "errMsg":"success",  //成功描述
     * "retData":{
     * "id":"fb9a120649ab510fc22d1b0adec0e006",     //文件唯一标识ID
     * "name":"ULUCU文档资料.zip",                   //原始文件名称,带后缀
     * "size":55941272,                             //文件大小
     * "url":"http://f1.afacms.com/fb/9a/fb9a120649ab510fc22d1b0adec0e006.zip", //文件访问/下载 url地址
     * }
     * "id":"WU_FILE_1"                             //页面端文件唯一识别ID
     * }
     *
     */
    public function upload_Action($encrypt = 0){
        //最长半小时有效时间
        set_time_limit(1800);

        //支持JS跨域上传文件,IE10以下浏览器不支持
        header('Access-Control-Allow-Origin：* ');

        if(isset($_SERVER['REQUEST_METHOD']) && 'OPTIONS' == $_SERVER['REQUEST_METHOD']){
            return $this->echojson($this->ret);
        }

        $filename = input::request('fileVal')?input::request('fileVal'):'file';
        $file = input::file($filename);

        //使用二进制传输标识
        $sendbybinary = false;
        if(!empty( $file )) {
            if ($file['error'] !== 0) {
                $this->ret['errNum'] = 1;
                $this->ret['errMsg'] = "文件上传失败";
                $this->ret['retData'] = $file;

                return $this->echojson($this->ret);
            }
            if(input::request('name') && input::request('type')) {
                $file['name'] = input::request('name');
                $file['type'] = input::request('type');
            }
        }else{
            $sendbybinary = true;
        }
//print_r(input::post());print_r($file);die;

        //支持post 和 二进制流 两种上传方式
        $post = array('name'=>input::request('name'),
            'chunk'=>input::request('chunk'),
            'chunks'=>input::request('chunks'),
            'id'=>input::request('id'));
        if($sendbybinary || intval($post["chunks"]) >= 1 ){
            $fileinfoArr = $this->uploadShard($post, $file);
            if(is_bool($fileinfoArr)) return $this->echojson($this->ret);
        }else {
            $fileinfoArr = Upload::saveHash($file);
        }

        if(false === $fileinfoArr){
            $this->ret['errNum'] = 1;
            $this->ret['errMsg'] = "文件上传失败";

            return $this->echojson($this->ret);
        }

        //文件信息保存入库
        $fileinfo = new fileinfo_Model();
        $fileinfo->account_id   = $this->account_id;
        $fileinfo->file_name    = input::request('name')?input::request('name'):$file['name'];
        $fileinfo->file_type    = input::request('type')?input::request('type'):$file['type'];
        $fileinfo->file_size    = isset($fileinfoArr['size'])?$fileinfoArr['size']:$file['size'];
        $fileinfo->add_time     = time();

        //加密时需要对返回的hash_id进行一次再加密
        if($encrypt == 1) {
            $fileinfo->hash_id = F::authstr($fileinfoArr['hash_id'], 'ENCODE');
        }else{
            $fileinfo->hash_id = $fileinfoArr['hash_id'];
        }

        $fileinfo->suffix       = $fileinfoArr['suffix'];
        $fileinfo->is_encrpt    = $encrypt == 1? 1: 0;

        $fileinfo->save();

        //$encrypt 为1时不返回文件地址, 否则返回地址
        $this->ret['retData'] = array('id'=>$fileinfo->hash_id, 'name'=>$fileinfo->file_name, 'size'=>$fileinfo->file_size);
        if($encrypt == 1){
            $this->ret['retData']['url'] = '';
        }else{
            $config = F::config('upload');
            if(isset($config['domain'])){
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR .'upload'. DIRECTORY_SEPARATOR, $config['domain'], $fileinfoArr['filename']);
            }else {
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR, F::config('domain'), $fileinfoArr['filename']);
            }
            $this->ret['retData']['url'] = $return_url;
        }

        return $this->echojson($this->ret);

    }

    /**
     * 上传文件-图片服务
     * token  GET  授权token,通过key在后端调用服务接口取得
     * name   GET/POST  文件名称,带后缀: 二进制传输时不能为空
     * type   GET/POST  文件类型: 二进制传输时不能为空
     * size   GET/POST  文件大小: 二进制传输时不能为空
     * chunk  GET/POST  当前分片编号,从0开始
     * chunks GET/POST  分片总数
     * id     GET/POST  上传文件ID WU_FILE_0
     * fileVal  GET/POST    存储上传文件的表单字段名称, 默认值为file
     * sizearr  GET/POST  图片尺寸, json数组字符串化,比如JSON.stringify([[1600,1600],[600,600]]) 表示生成两张图,大图为1600*1600,小图为600*600
     * 详细说明如下
     * 单张图片：JSON.stringify([[120,90]]) 后端服务直接调用,可以传 $arr = array('w'=>120, 'h'=>90); 或 $arr = array(array(120,90); 或 array(120,90);
     * 大小图片，JSON.stringify([[400,300],[120,90]]) 即两张图片, 后端服务直接调用,可以传：$arr = array(array(400,300),array(120,90)); array(400,300)为大图宽高，array(120,90)为小图宽高
     * 大中小图，JSON.stringify([[1200,900],[400,300],[120,90]]) 即三张图片, 后端服务直接调用,可以传：$arr = array(array(1200,900),array(400,300),array(120,90)); 依次为大中小图的尺寸大小
     *
     * file 普通表单file类型上传
     * 或 二进制类型文件内容上传
     *
     * @param boolean $encrypt :0或1
     * 0 为不加密, 默认为0, 为0时将会直接返回可访问的文件地址;
     * 1为加密, 不会返回可访问地址,需要经过授权, 通过指定接口才能访问;
     *
     * @return json {
     * "errNum":0,          //0标识成功
     * "errMsg":"success",  //成功描述
     * "retData":{
     * "id":"fb9a120649ab510fc22d1b0adec0e006",     //文件唯一标识ID
     * "name":"ULUCU文档资料.zip",                   //原始文件名称,带后缀
     * "size":55941272,                             //文件大小
     * "url":"http://f1.afacms.com/fb/9a/fb9a120649ab510fc22d1b0adec0e006.zip", //文件访问/下载 url地址
     * }
     * "id":"WU_FILE_1"                             //页面端文件唯一识别ID
     * }
     *
     */
    public function uploadPic_Action($encrypt = 0){
        //最长半小时有效时间
        set_time_limit(1800);

        ini_set ('memory_limit', '256M');
        //支持JS跨域上传文件,IE10以下浏览器不支持
        header('Access-Control-Allow-Origin: * ');

        if(isset($_SERVER['REQUEST_METHOD']) && 'OPTIONS' == $_SERVER['REQUEST_METHOD']){
            return $this->echojson($this->ret);
        }

        $filename = input::request('fileVal')?input::request('fileVal'):'file';
        $file = input::file($filename);

        $this->ret['id'] = input::request('id');

        if(empty($file)){
            //二进制上传图片文件支持
            $fileinfoArr = Upload::createRandFile();
            $postion = strrpos(input::request('name'), '.');
            $suffix = substr(input::request('name'), $postion);
            $fileinfoArr['filename'] = $fileinfoArr['filename'].$suffix;
            $fileinfoArr['suffix'] = substr($suffix, 1);

            $uploadPath = $fileinfoArr['filename'];

            $out = @fopen($uploadPath, "wb");
            if (!$in = @fopen("php://input", "rb")) {
                $this->ret['errNum'] = 101;
                $this->ret['errMsg'] = "Failed to open input stream.";
                return false;
            }
            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }
            @fclose($out);
            @fclose($in);

            $file['name'] = input::request('name');
            $file['type'] = input::request('type');
        }else {
            if ($file['error'] !== 0) {
                $this->ret['errNum'] = 1;
                $this->ret['success'] = "文件上传失败";
                $this->ret['retData'] = $file;

                return $this->echojson($this->ret);
            }

            if (input::request('name') && input::request('type')) {
                $file['name'] = input::request('name');
                $file['type'] = input::request('type');
            }
            //print_r(input::post());print_r($file);die;
            $fileinfoArr = Upload::saveHash($file);
        }

        $size_arr = input::request('sizearr')?json_decode(input::request('sizearr'), true):array();

        if(is_array($fileinfoArr)) {
            $return_arr = $this->imageScaling($fileinfoArr['filename'], $size_arr);
            if (is_array($return_arr)) {
                $last_point = strrpos($return_arr['url'], '/');

                //图片文件信息保存入库
                $fileinfo = new fileinfo_Model();
                $fileinfo->account_id = $this->account_id;
                $fileinfo->file_name = $file['name'];
                $fileinfo->file_type = $file['type'];
                $fileinfo->file_size = input::request('size')?input::request('size'):$file['size'];;
                $fileinfo->add_time = time();

                //加密时需要对返回的hash_id进行一次再加密
                if($encrypt == 1) {
                    $fileinfo->hash_id = F::authstr($fileinfoArr['hash_id'], 'ENCODE');
                }else{
                    $fileinfo->hash_id = $fileinfoArr['hash_id'];
                }

                $fileinfo->suffix = $fileinfoArr['suffix'];
                $fileinfo->is_encrpt    = $encrypt == 1? 1: 0;

                $fileinfo->extend_text = json_encode(array('type' => 'image', 'count' => $return_arr['count'], 'hash_url' => substr($return_arr['url'], $last_point + 1)));

                $fileinfo->save();

                $this->ret['retData'] = array(
                    'id' => $fileinfo->hash_id,
                    'name' => $fileinfo->file_name,
                    'size' => $fileinfo->file_size,
                    'suffix' => $fileinfo->suffix,
                    'url' => $return_arr['url']
                );
                //$encrypt 为1时不返回文件地址, 否则返回地址
                $this->ret['retData']['url'] = $encrypt == 1?'':$return_arr['url'];

                return $this->echojson($this->ret);
            }
        }

        $this->ret['errNum'] = 1;
        $this->ret['errMsg'] = "文件上传失败";

        return $this->echojson($this->ret);


    }

    /**
     * 下载文件
     *
     * get 传值, token为回话授权token, 有效期300s
     *
     * @param string $hash_id 文件唯一标识
     * @return 文件内容
     */
    public function download_Action($hash_id = ''){

        //token正常且在有效时间内, 有效时间为300秒
        if($this->token && time()-$this->token_time <= 300){
            $fileinfo = new fileinfo_Model();
            $fileinfo->getByHashId($hash_id);
            if($fileinfo->id && $this->account_id == $fileinfo->account_id ){
                $real_hash_id = F::authstr($hash_id, 'DECODE');

                // 组装文件路径
                $directory = Upload::getDirectory($real_hash_id);

                //真实文件路径
                $file_path = $directory.DIRECTORY_SEPARATOR.$real_hash_id.'.'.$fileinfo->suffix;

                if(file_exists($file_path)){
                    //存在--打开文件
                    $fp = fopen($file_path, "r");

                    //http 下载需要的响应头
                    header("Content-type: application/octet-stream"); //返回的文件
                    header("Accept-Ranges: bytes");   //按照字节大小返回
                    header("Accept-Length: " . $fileinfo->file_size); //返回文件大小
                    header("Content-Disposition: attachment; filename=" . $fileinfo->file_name);//这里客户端的弹出对话框，对应的文件名
                    //向客户端返回数据
                    //设置大小输出
                    $buffer = 1024;
                    //为了下载安全，我们最好做一个文件字节读取计数器
                    $file_count = 0;
                    //判断文件指针是否到了文件结束的位置(读取文件是否结束)
                    while (!feof($fp) && ($fileinfo->file_size - $file_count) > 0) {
                        $file_data = fread($fp, $buffer);
                        //统计读取多少个字节数
                        $file_count += $buffer;
                        //把部分数据返回给浏览器
                        echo $file_data;
                    }
                    //关闭文件
                    fclose($fp);

                    $this->ret['retData'] = 'file binary content';
                    return true;
                }
            }
            $this->ret['errNum'] = 1;
            $this->ret['errMsg'] = "file not exist!";
            return $this->echojson($this->ret);
        }else{
            $this->ret['errNum'] = 1;
            $this->ret['errMsg'] = "token is invalid!";
            return $this->echojson($this->ret);
        }

    }

    /**
     * 显示图片
     *
     * get 传值, token为回话授权token, 有效期300s
     *
     * @param string $hash_id 文件唯一标识
     * @param string $flag:当访问图片时才有用, s为小图, b为大图, m为中图
     * @return 文件内容
     */
    public function showImage_Action($hash_id = '', $flag = ''){

        //token正常且在有效时间内, 有效时间为300秒
        if($this->token && time()-$this->token_time <= 300){
            $fileinfo = new fileinfo_Model();
            $fileinfo->getByHashId($hash_id);
            if($fileinfo->id  && $this->account_id == $fileinfo->account_id){
                $real_hash_id = F::authstr($hash_id, 'DECODE');

                // 组装文件路径
                $directory = Upload::getDirectory($real_hash_id);

                //判断图片格式
                if (in_array($fileinfo->suffix, array('png', 'jpg', 'gif'))) {
                    if($fileinfo->extend_text) {
                        $extend_arr = json_decode($fileinfo->extend_text, true);
                        if ($extend_arr['count'] == 1) {
                            $file_path = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '.' . $fileinfo->suffix;
                        } else {
                            $suffix_flag = empty($flag) ? '_s' : '_' . $flag;
                            $file_path = $directory . DIRECTORY_SEPARATOR . $real_hash_id . $suffix_flag . '.' . $fileinfo->suffix;
                        }
                    }else{
                        $file_path = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '.' . $fileinfo->suffix;
                    }
                    if(file_exists($file_path)){

                        header("Content-type: ".$fileinfo->file_type); //返回的文件
                        header("Accept-Ranges: bytes");   //按照字节大小返回
                        header("Accept-Length: " . $fileinfo->file_size); //返回文件大小

                        echo file_get_contents($file_path);

                        $this->ret['retData'] = 'image binary content';
                        return true;
                    }
                }
            }
            $this->ret['errNum'] = 1;
            $this->ret['errMsg'] = "file not exist!";
            return $this->echojson($this->ret);
        }else{
            $this->ret['errNum'] = 1;
            $this->ret['errMsg'] = "token is invalid!";
            return $this->echojson($this->ret);
        }

    }

    /**
     * 对图片进行缩放
     * @param string $image_file : image file 完整物理路径地址
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
    private function imageScaling($image_file, $arr = array(), $quality = 90){

        $image = Image::instance($image_file);
        $has_small = false;//是否有略缩图

        if (isset($arr['w']) || isset($arr['h']) || is_numeric($arr[0])){
            if(isset($arr['w'])) {
                $width = $arr['w'];
                $height = $arr['h'];
            }else{
                $width = $arr[0];
                $height = $arr[0];
            }
            $image->resize($width, $height)->save(NULL, $quality);
            $count = 1;
        }else{

            $last_point = strripos($image_file, '.');
            $prefix = substr($image_file, 0, $last_point);
            $suffix = substr($image_file, $last_point+1);

            $count = count($arr);
            if (1 == $count){
                $arr = $arr[0];
                $image->resize(isset($arr[0])?$arr[0]:null, isset($arr[1])?$arr[1]:null)->save(NULL, $quality);
            }elseif (2 == $count){

                $image->resize(@$arr[0][0],@$arr[0][1])->save($prefix.'_b'.'.'.$suffix, $quality);
                $image->resize(@$arr[1][0],@$arr[1][1])->save($prefix.'_s'.'.'.$suffix, $quality);
                $has_small = true;
            }elseif (3 == $count){
                $image->resize(@$arr[0][0],@$arr[0][1])->save($prefix.'_b'.'.'.$suffix, $quality);
                $image->resize(@$arr[1][0],@$arr[1][1])->save($prefix.'_m'.'.'.$suffix, $quality);
                $image->resize(@$arr[2][0],@$arr[2][1])->save($prefix.'_s'.'.'.$suffix, $quality);
                $has_small = true;
            }else{
                return false;
            }
        }

        if ($has_small){
            $filesmall = $prefix.'_s'.'.'.$suffix;

            $config = F::config('upload');
            if(isset($config['domain'])){
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR .'upload'. DIRECTORY_SEPARATOR, $config['domain'], $filesmall);
            }else {
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR, F::config('domain'), $filesmall);
            }

            //删除原图
            unlink($image_file);
        }else{
            $config = F::config('upload');
            if(isset($config['domain'])){
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR .'upload'. DIRECTORY_SEPARATOR, $config['domain'], $image_file);
            }else {
                $return_url = str_replace(DOCROOT . DIRECTORY_SEPARATOR, F::config('domain'), $image_file);
            }
        }
        return array('count'=>$count, 'url'=>$return_url);
    }

    /**
     * 删除文件或图片
     *
     * @param string $hash_id 文件唯一标识
     * @return boolean
     */
    public function delete_Action($hash_id = ''){

        //token正常且在有效时间内, 有效时间为300秒

        $fileinfo = new fileinfo_Model();
        $fileinfo->getByHashId($hash_id);
        if ($fileinfo->id && $this->account_id == $fileinfo->account_id) {
            if($fileinfo->is_encrpt){
                $real_hash_id = F::authstr($hash_id, 'DECODE');
            }else{
                $real_hash_id = $hash_id;
            }
            // 组装文件路径
            $directory = Upload::getDirectory($real_hash_id);

            if(in_array($fileinfo->suffix, array('png','jpg','gif'))){
                if($fileinfo->extend_text){
                    $extend_text = json_decode($fileinfo->extend_text, true);
                    if($extend_text['count'] == 1){
                        $file_path = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '.' . $fileinfo->suffix;
                        file_exists($file_path) && @unlink($file_path);
                    }elseif($extend_text['count'] == 2){
                        $file_path1 = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '_s.' . $fileinfo->suffix;
                        $file_path2 = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '_b.' . $fileinfo->suffix;

                        file_exists($file_path1) && @unlink($file_path1);
                        file_exists($file_path2) && @unlink($file_path2);
                    }elseif($extend_text['count'] == 3){
                        $file_path1 = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '_s.' . $fileinfo->suffix;
                        $file_path2 = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '_m.' . $fileinfo->suffix;
                        $file_path3 = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '_b.' . $fileinfo->suffix;

                        file_exists($file_path1) && @unlink($file_path1);
                        file_exists($file_path2) && @unlink($file_path2);
                        file_exists($file_path3) && @unlink($file_path3);
                    }
                }


            }else {
                //真实文件路径
                $file_path = $directory . DIRECTORY_SEPARATOR . $real_hash_id . '.' . $fileinfo->suffix;
                file_exists($file_path) && @unlink($file_path);
            }

            $fileinfo->delete();

            $this->ret['retData'] = 'delete ok';
            return $this->echojson($this->ret);
        }
        $this->ret['errNum'] = 1;
        $this->ret['errMsg'] = "file not exist!";
        return $this->echojson($this->ret);


    }


    /**
     * 大文件切片上传
     * @param $post
     * @param $file_arr
     * @return bool/array
     */
    private function uploadShard($post, $file_arr){
        //临时存放目录
        $tempDir = DOCROOT . DIRECTORY_SEPARATOR. 'temp'.DIRECTORY_SEPARATOR;

        $cleanupTargetDir = true; // 开启文件缓存删除
        $maxFileAge = 60*60*24; // 文件缓存时间超过时间自动删除

        // Post 或 file 方式获取文件名
        $fileName = isset($post["name"])?$post["name"]:$file_arr['name'];

        $oldName = $fileName;//记录文件原始名字
        $filePath = $tempDir . $fileName;

        // Chunking might be enabled
        $chunk = isset($post["chunk"]) ? intval($post["chunk"]) : 0;
        $chunks = isset($post["chunks"]) ? intval($post["chunks"]) : 1;
        // 删除缓存校验
        if ($cleanupTargetDir) {
            $dir = opendir($tempDir);
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $tempDir  . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp|mp4|pptx|ppt|mp3|amr|m4a|png|jpg|gif)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        //$this->ret['jsonrpc'] = "2.0";
        $this->ret['id'] = @$post['id'];

        // 打开并写入缓存文件
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $this->ret['errNum'] = 102;
            $this->ret['errMsg'] = "Failed to open output stream";
            return false;
        }
        if (!empty($file_arr) && @$file_arr["tmp_name"]) {
            if ($file_arr["error"] || !is_uploaded_file($file_arr["tmp_name"])) {
                $this->ret['errNum'] = 103;
                $this->ret['errMsg'] = "Failed to move uploaded file.";
                return false;
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($file_arr["tmp_name"], "rb")) {
                $this->ret['errNum'] = 101;
                $this->ret['errMsg'] = "Failed to open input stream.";
                return false;
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $this->ret['errNum'] = 101;
                $this->ret['errMsg'] = "Failed to open input stream.";
                return false;
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        //文件全部上传 执行合并文件
        if ( $done ) {
            if(empty($file_arr)){
                $saveinfo = Upload::createRandFile();
                $postion = strrpos($post['name'], '.');
                $suffix = substr($post['name'], $postion);
                $saveinfo['filename'] = $saveinfo['filename'].$suffix;
                $saveinfo['suffix'] = substr($suffix, 1);

                $uploadPath = $saveinfo['filename'];
            }else {
                $saveinfo = Upload::getSaveInfo($file_arr);
                $uploadPath = $saveinfo['filename'];
            }
            if (!$out = @fopen($uploadPath, "wb")) {
                $this->ret['errNum'] = 102;
                $this->ret['errMsg'] = "Failed to open output stream.";
                return false;
            }
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);

            //读取文件大小
            $saveinfo['size'] = filesize($uploadPath);

            return $saveinfo;
        }
        $this->ret['retData']['url'] = '';
        return true;
    }



}
