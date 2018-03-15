<?php
/**
 * Created by PhpStorm.
 * User: ZSF
 * Date: 16/12/29
 * Time: 下午4:00
 */

class Test_Controller extends Controller
{


    static function RandAbc($length = "")
    { // 返回随机字符串
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        return str_shuffle($str);
    }

    public function encode_Action(){
        //$filename = APPPATH.'controller/server.php'; //要加密的文件

        $filename = PROROOT.DIRECTORY_SEPARATOR.'htmlroot'.DIRECTORY_SEPARATOR.'doencode.php';

        $T_k1 = self::RandAbc(); //随机密匙1
        $T_k2 = self::RandAbc(); //随机密匙2
        $vstr = file_get_contents($filename);
        $v1 = base64_encode($vstr);
        $c = strtr($v1, $T_k1, $T_k2); //根据密匙替换对应字符。
        $c = $T_k1.$T_k2.$c;
        $q1 = "O00O0O";
        $q2 = "O0O000";
        $q3 = "O0OO00";
        $q4 = "OO0O00";
        $q5 = "OO0000";
        $q6 = "O00OO0";
        $s = '$'.$q6.'=urldecode("%6E1%7A%62%2F%6D%615%5C%76%740%6928%2D%70%78%75%71%79%2A6%6C%72%6B%64%679%5F%65%68%63%73%77%6F4%2B%6637%6A");$'.$q1.'=$'.$q6.'{3}.$'.$q6.'{6}.$'.$q6.'{33}.$'.$q6.'{30};$'.$q3.'=$'.$q6.'{33}.$'.$q6.'{10}.$'.$q6.'{24}.$'.$q6.'{10}.$'.$q6.'{24};$'.$q4.'=$'.$q3.'{0}.$'.$q6.'{18}.$'.$q6.'{3}.$'.$q3.'{0}.$'.$q3.'{1}.$'.$q6.'{24};$'.$q5.'=$'.$q6.'{7}.$'.$q6.'{13};$'.$q1.'.=$'.$q6.'{22}.$'.$q6.'{36}.$'.$q6.'{29}.$'.$q6.'{26}.$'.$q6.'{30}.$'.$q6.'{32}.$'.$q6.'{35}.$'.$q6.'{26}.$'.$q6.'{30};eval($'.$q1.'("'.base64_encode('$'.$q2.'="'.$c.'";eval(\'?>\'.$'.$q1.'($'.$q3.'($'.$q4.'($'.$q2.',$'.$q5.'*2),$'.$q4.'($'.$q2.',$'.$q5.',$'.$q5.'),$'.$q4.'($'.$q2.',0,$'.$q5.'))));').'"));';

        $s = '<?php '."\n".$s."\n".' ?>';
        //echo $s;
        // 生成 加密后的PHP文件
        $fpp1 = fopen(PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode2.php', 'w');
        fwrite($fpp1, $s) or die('写文件错误');
    }

    public function decode_Action(){
        include_once PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode.php';
        mygad();
    }

    public function encode2_Action(){
        $filename = PROROOT.DIRECTORY_SEPARATOR.'htmlroot'.DIRECTORY_SEPARATOR.'doencode.php';
        $type = strtolower(substr(strrchr($filename, '.'), 1));
        if ('php' == $type && is_file($filename)) { // 如果是PHP文件 并且可写 则进行压缩编码
            $contents = file_get_contents($filename); // 判断文件是否已经被编码处理
            $contents = php_strip_whitespace($filename);

            // 去除PHP头部和尾部标识
            $headerPos = strpos($contents, '<?php');
            $footerPos = strrpos($contents, '?>');
            $contents = substr($contents, $headerPos + 5, $footerPos - $headerPos);
            $encode = base64_encode(gzdeflate($contents)); // 开始编码
            $encode = '<?php' . "\n eval(gzinflate(base64_decode(" . "'" . $encode . "'" . ")));\n\n?>";

            $filstore = PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode3.php';
            echo $filstore;
            return file_put_contents($filstore, $encode);
        }
        return false;

    }

    public function decode2_Action(){
        include_once PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode3.php';


        echo gzinflate(base64_decode('41JIK81LLsnMz1PIrUxPTNHQrFZITc7IV1BPzUvOT0lVeNq14On6NnVrhVoFezsA'));


        mygad();
    }


    public function encode3_Action(){
        $filename = PROROOT.DIRECTORY_SEPARATOR.'htmlroot'.DIRECTORY_SEPARATOR.'doencode.php';
        $type = strtolower(substr(strrchr($filename, '.'), 1));
        if ('php' == $type && is_file($filename)) { // 如果是PHP文件 并且可写 则进行压缩编码
            $contents = file_get_contents($filename); // 判断文件是否已经被编码处理
            $contents = php_strip_whitespace($filename);

            // 去除PHP头部和尾部标识
            $headerPos = strpos($contents, '<?php');
            $footerPos = strrpos($contents, '?>');
            $contents = substr($contents, $headerPos + 5, $footerPos - $headerPos);
            $encode = base64_encode(gzdeflate($contents)); // 开始编码

            //$encode = '<?php' . "\n eval(gzinflate(base64_decode(" . "'" . $encode . "'" . ")));\n\n";
            $encode = "\n eval(gzinflate(base64_decode(" . "'" . $encode . "'" . ")));\n\n";

            $encode = textauth::instance()->encrypt($encode, 'afaphp.com');

            $filstore = PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode33.php';

            return file_put_contents($filstore, $encode);
        }
        return false;

    }

    public function decode3_Action(){
        $str = file_get_contents( PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'temp_do_encode33.php');

        echo eval(textauth::instance()->decrypt($str, 'afaphp.com'));



        mygad();
    }

    public function encode4_Action(){
        Encodesource_encode_Service::encodeSource('doencode.php');
        echo 'ok';
    }

    public function decode4_Action(){

        $filepath =  PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'tempfile'.DIRECTORY_SEPARATOR.'doencode.php';

        F::benchmark('eee');

        include_once $filepath;

        F::benchmark('fff');

        mygad();
    }

    public function encode5_Action(){
        $filename = PROROOT.DIRECTORY_SEPARATOR.'htmlroot'.DIRECTORY_SEPARATOR.'important'.DIRECTORY_SEPARATOR.'doencode.php';
        $text = textauth::instance()->encrypt(php_strip_whitespace($filename), 'afaphp.com');

        $decode = tdecode::instance()->decrypt($text, 'afaphp.com');

        $decode = substr($decode, 5, strlen($decode)-2);


        eval ($decode);

        mygad();

       // ('doencode.php');
       // echo 'ok';
    }

    public function ser_Action(){

        $author = new Author_author_Service();

        print_r($author->get(1));

        $ser = new dotest_Service();
        $ser->doit();

       // ('doencode.php');
       // echo 'ok';
    }


}