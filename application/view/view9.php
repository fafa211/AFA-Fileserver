<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>rqcode test jquery</title>

    <script type="text/javascript" src="/static/js/libs/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/jquery.qrcode.min.js"></script>

    <script type="text/javascript">
        function generateQRCode(rendermethod, picwidth, picheight, url, el) {

            $(el).qrcode({
                render: rendermethod, // 渲染方式有table方式（IE兼容）和canvas方式
                width: picwidth, //宽度
                height:picheight, //高度
                text: utf16to8(url), //内容
                typeNumber:-1,//计算模式
                correctLevel:2,//二维码纠错级别
                background:"#ffffff",//背景颜色
                foreground:"#000000"  //二维码颜色

            });
        }
        function init() {
            $(".qrcode").each(function(i){
                generateQRCode("canvas",200, 200, $(this).attr('data-text'), this);console.log(this);//canvas  or  table
                var margin = ($(this).height()-$(".codeico", this).height())/2;
                $(".codeico", this).css("margin",margin);


            })

//            generateQRCode("table",200, 200, "http://www.szjs.com/?test=0000","#qrcode1");
//            var margin = ($("#qrcode1").height()-$("#qrcode1 .codeico").height())/2;
//            $("#qrcode1 .codeico").css("margin",margin);
//            generateQRCode("table",200, 200, "http://www.szjs.com/?test=0000","#qrcode2");
//            var margin = ($("#qrcode2").height()-$("#qrcode2 .codeico").height())/2;
//            $("#qrcode2 .codeico").css("margin",margin);
//            generateQRCode("table",200, 200, "http://www.szjs.com/?test=0000","#qrcode3");
//            var margin = ($("#qrcode3").height()-$("#qrcode3 .codeico").height())/2;
//            $("#qrcode3 .codeico").css("margin",margin);


        }
        //中文编码格式转换
        function utf16to8(str) {
            var out, i, len, c;
            out = "";
            len = str.length;
            for (i = 0; i < len; i++) {
                c = str.charCodeAt(i);
                if ((c >= 0x0001) && (c <= 0x007F)) {
                    out += str.charAt(i);
                } else if (c > 0x07FF) {
                    out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
                    out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F));
                    out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
                } else {
                    out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F));
                    out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
                }
            }
            return out;
        }

    </script>

    <style type="text/css">
        .codeico{
            position:absolute;/**生成绝对定位的元素，相对于浏览器窗口进行定位。元素的位置通过 "left", "top", "right" 以及 "bottom"**/
            z-index:9999999;
            width:30px;
            height:30px;
            background:url(/static/images/icons/iconlogo.png) no-repeat;
        }
    </style>
</head>
<body onLoad="init()">
<h1>Qrcode</h1>
<div class="qrcode" id="qrcode1" data-text="测试">
    <div class="codeico"></div>
</div>

<div class="qrcode" id="qrcode2" data-text="http://www.baidu.com/" style="margin-top: 100px;">
    <div class="codeico"></div>
</div>

<div class="qrcode" id="qrcode3" data-text="http://www.szjs.com/?test=0000" style="margin-top: 100px;">
    <div class="codeico"></div>
</div>

</body>
</html>