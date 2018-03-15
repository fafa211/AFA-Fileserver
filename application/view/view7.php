<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>测试View</title>
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <script src="/static/js/libs/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
    <script src="http://fs.szjs.cn/static/webuploader/uploadfile.js"></script>
    <script src="http://fs.szjs.cn/static/webuploader/uploadpic.js"></script>

    <script type="text/javascript">
        var uploaderToken = "<?php echo $token;?>";
    </script>
</head>
<body>
<script type="text/javascript">
    webupload_fileinit({token:uploaderToken, fieldname:"file1", sendAsBinary:true});
    webupload_fileinit({token:uploaderToken, fieldname:"file2", fileNumLimit:3});
    webupload_imginit({token:uploaderToken, sizearr:JSON.stringify([[600,600],[100,100]]), fieldname:"fileimg", fileNumLimit:3, sendAsBinary:true});
</script>

  <!--
<div class="uploader-container">

    <div id="thelist" class="uploader-list"></div>
    <div class="btns">
        <div id="picker" style="overflow: hidden;float: left;">选择文件</div>
        <button id="ctlBtn" class="btn btn-default" style="float: left">开始上传</button>
    </div>
    <div class="clear" style="clear: both;"></div>
    <input type="hidden" name="file" id="inputfile" value="" />
</div>
-->




<script type="text/javascript">

//    var BASE_URL = "<?php //echo $api_domain;?>//";
//
//    var uploader = WebUploader.create({
//
//        // swf文件路径
//        swf: BASE_URL + 'static/webuploader/Uploader.swf',
//
//        // 文件接收服务端。
//        server: BASE_URL+'fileServer/upload?token=<?php //echo $token;?>//',
//
//        // 选择文件的按钮。可选。
//        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
//        pick: '#picker',
//
//        chunked: true, //是否要分片处理大文件上传
//        chunkSize:2*1024*1024, //分片上传，每片2M，默认是5M
//        //auto: false //选择文件后是否自动上传
//        chunkRetry : 2, //如果某个分片由于网络问题出错，允许自动重传次数
//        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
//        resize: false
//    });
//
//    // 当有文件被添加进队列的时候
//    uploader.on( 'fileQueued', function( file ) {
//        $('#thelist').append( '<div id="' + file.id + '" class="item">' +
//            '<h4 class="info">' + file.name + '</h4>' +
//            '<p class="state">等待上传...</p>' +
//            '</div>' );
//    });
//
//    // 文件上传过程中创建进度条实时显示。
//    uploader.on( 'uploadProgress', function( file, percentage ) {
//        var $li = $( '#'+file.id ),
//            $percent = $li.find('.progress .progress-bar');
//
//        // 避免重复创建
//        if ( !$percent.length ) {
//            $percent = $('<div class="progress progress-striped active">' +
//                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
//                '</div>' +
//                '</div>').appendTo( $li ).find('.progress-bar');
//        }
//
//        $li.find('p.state').text('上传中');
//
//        $percent.css( 'width', percentage * 100 + '%' );
//    });
//
//    uploader.on( 'uploadSuccess', function( file, response ) {
//        $( '#'+file.id ).find('p.state').text('已上传');
//        //console.log(file);
//        console.log(response);
//    });
//
//    uploader.on( 'uploadError', function( file ) {
//        $( '#'+file.id ).find('p.state').text('上传出错');
//    });
//
//    uploader.on( 'uploadComplete', function( file, response ) {
//        $( '#'+file.id ).find('.progress').fadeOut();
//    });
//
//
//    $('#ctlBtn').click(function(){
//        uploader.upload();
//    })
//


</script>

</body>
</html>