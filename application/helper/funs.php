<?php

/**
 * Created by PhpStorm.  常用静态方法库
 * User: ZSF
 * Date: 16/7/27
 * Time: 下午4:07
 */
class funs
{
    /**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    public static function isCreditNo($vStr)
    {
        $vCity = array(
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18)
        {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18)
        {
            $vSum = 0;

            for ($i = 17 ; $i >= 0 ; $i--)
            {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }

            if($vSum % 11 != 1) return false;
        }

        return true;
    }

    /**
     *
     * 解析JS对象字符串
     * @param $str
     * @param $return 返回结果类型
     * @return array|object|空字符串
     */
    public static function parseJsObjectString($str = '', $return = 'array'){

        $str = trim($str);
        if(empty($str)) return '';

        $str = substr($str, stripos($str, '{')+1);
        if(substr($str, -1) == '}') {
            $str = substr($str, 0, -1);
        }
        $strArr = explode(',', $str);

        $ret = array();
        foreach($strArr as $s){
            list($k, $v) = explode(':', $s);
            $ret[trim($k)] = str_replace(array('\'','"'),'',trim($v));
        }

        return $return == 'array'?$ret:(object)$ret;
    }

    /**
     * @param $text  内容:地址或文本 必填
     * @param bool $outfile 输出保存文件
     * @param int $level 容错率，也就是有被覆盖的区域还能识别 L=>7% M=>15% Q=>25% H=>30%
     * @param int $size 生成图片大小，默认是3
     * @param int $margin 二维码周围边框空白区域间距值
     * @param bool $saveandprint 是否保存二维码并 显示
     * @param bool/string $logo logo地址,没有则默认为false
     */
    public static function QRCode($text, $outfile=false, $level="L", $size=3, $margin=4, $saveandprint=false, $logo = false){
        include_once "phpqrcode".DIRECTORY_SEPARATOR."phpqrcode.php";

        if(false === $logo) {
            QRcode::png($text, $outfile, $level, $size, $margin, $saveandprint);
        }else{

            //文件唯一标识ID
            $hash_id = md5(uniqid());
            //文件名称
            $filename = $hash_id . '.png';
            //文件存储目录
            $directory = Upload::getDirectory($hash_id);
            //创建文件存储目录
            $flag = common::createDir($directory);
            if(!$flag) {
                throw new Exception('Directory '.$directory.' must be writable', E_ERROR);
                return false;
            }
            // Make the filename into a complete path
            $filename = $directory.DIRECTORY_SEPARATOR.$filename;

            //生成二维码图片
            QRcode::png($text, $filename, $level, $size, 2, $margin);
            $logodir = DOCROOT.'/static/images/icons/';
            if(!file_exists($logo)){
                $logo = $logodir.$logo;
                if(!file_exists($logodir.$logo)){
                    $logo = $logodir.'iconlogo.png';//默认
                }
            }

            //已经生成的原始二维码图
            $QR = $filename;

            if ($logo !== FALSE) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新组合图片并调整大小
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
            }
            if(false === $outfile) {
                //输出图片
                Header("Content-type: image/png");
                ImagePng($QR);
            }else{
                ImagePng($QR, $filename);
            }
        }

    }

}