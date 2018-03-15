<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>rqcode test jquery</title>

    <script type="text/javascript" src="/static/js/libs/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/jquery.qrcode.min.js"></script>

    <style type="text/css">
        *html{background-attachment:fixed;}/*解决IE6下滚动抖动的问题*/

        /*解决IE6下不兼容 position:fixed 的问题*/

        #code,#code_hover{width:38px;height:76px;position:fixed; right:50px;cursor:pointer;_position:absolute;_bottom:auto;}

        #code{background-position:0px 0px;bottom:130px;_margin-bottom:130px;}

        #code_hover{background-position:-39px 0px;bottom:130px;_margin-bottom:130px;}

        #code_img{width:282px;height:282px;border:1px solid red;background:#ccc;position:fixed;right:125px;bottom:130px;cursor:pointer;display:none;_position:absolute;_bottom:auto;_margin-bottom:67px; z-index:9999999998;}

        #qrcode{z-index:9999999999; margin:4px;border:1px solid red;}

        #codeico{border:1px solid red; position:fixed;_position:absolute;margin:126px;z-index:99999997;width:30px; height:30px;background:url("/static/images/icons/iconlogo.png") no-repeat;}
    </style>
</head>
<body>

<div id="qrcode1" style="margin-bottom: 50px;margin-top:600px;"></div>
<div id="qrcode2" style="margin-bottom: 50px;"></div>
<div id="qrcode3" style="margin-bottom: 50px;"></div>
<div id="qrcode4" style="margin-bottom: 50px;"></div>

<div id="code"></div>
<div id="code_img"><div id="qrcode"><div id="codeico"></div></div></div>

<img src="<?php //funs::QRCode('http://www.baidu.com/?flag=fafa211');?>">

<script type="text/javascript">

    jQuery('#qrcode1').qrcode("http://blog.csdn.net/mr_smile2014");
    jQuery('#qrcode2').qrcode({render:"canvas",width: 164,height: 164,correctLevel:0,text: "http://blog.csdn.net/mr_smile2014"});
    jQuery('#qrcode3').qrcode({width: 200,height: 200,correctLevel:0,render: "table",text: "http://blog.csdn.net/mr_smile2014"});

    //jQuery('#qrcode').qrcode({width: 282,height: 282,correctLevel:0,render: "table",text: "http://www.szjs.com/"});
</script>
</body>
</html>