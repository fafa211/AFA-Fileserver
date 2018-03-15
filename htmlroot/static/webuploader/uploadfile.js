/**
 * Created by zhengshufa on 17/7/29.
 * 基于jquery & webupload
 */

//文件服务器域名配置
if("undefined" == typeof webupload_domain) var webupload_domain = 'http://fs.afacms.com';
if('undefined' == typeof $.webupload_path) {
    $.extend({
        webupload_path: webupload_domain+'/static/webuploader/',//webupload目录完整地址,须根据实际环境进行配置
        webupload_include: function (file) {//自动载入JS/CSS文件
            var files = typeof file == "string" ? [file] : file;
            for (var i = 0; i < files.length; i++) {
                var name = files[i].replace(/^s+$/g, "");console.log(name);
                var att = name.split('.');
                var ext = att[att.length - 1].toLowerCase();
                var isCSS = ext == "css";
                var tag = isCSS ? "link" : "script";
                var attr = isCSS ? " type='text/css' rel='stylesheet' " : " language='javascript' type='text/javascript' ";
                var link = (isCSS ? "href" : "src") + "='" + $.webupload_path + name + "'";
                if ($(tag + "[" + link + "]").length == 0) {
                    document.write("<" + tag + attr + link + "></" + tag + ">");
                }
            }
        }
    });
    $.webupload_include(['webuploader.css', 'demo.css', 'webuploader.js']);
}
$.getScript()
if("undefined" == typeof webupload_flash) var webupload_flash = $.webupload_path+'Uploader.swf';
if("undefined" == typeof webupload_fileserver) var webupload_fileserver = webupload_domain+'/fileServer/upload?token=';
if("undefined" == typeof webupload_fileVal) var webupload_fileVal = 'myfile';

//默认配置
var webupload_file_config = {
    // 选完文件后，是否自动上传。
    auto: false,
    // swf文件路径
    swf: webupload_flash,
    // 文件接收服务端。
    server: webupload_fileserver,
    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '.uploader-container .pick',
    // 只允许选择图片文件。
    accept: {
        title: 'Files',
        //默认支持的文件格式
        extensions: 'txt,pdf,ppt,pptx,mp4,mp3,avi,doc,docx,jpg,png,gif,xls,xlsx,zip,gz,rar,amr,m4a',
    },
    formData:{"fileVal": webupload_fileVal},
    fileVal: webupload_fileVal,
    fileNumLimit:1,  //文件数量限制
    fileSingleSizeLimit:1024*1024*1024,  //单个文件大小限制为1G
    chunked: true, //是否要分片处理大文件上传
    chunkSize:1*1024*1024, //分片大小，默认是5M
    //auto: false //选择文件后是否自动上传
    chunkRetry : 2, //如果某个分片由于网络问题出错，允许自动重传次数
    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
    resize: false
}

//上传的实体
var uploadFileArr = [];

function webuploadFiles(config){
    var realconfig = $.extend({}, webupload_file_config, config);
    realconfig.server = webupload_file_config.server+config.token;

    //console.log(realconfig);
    //console.log(realconfig.server);
    // 初始化Web Uploader
    var uploader = WebUploader.create(realconfig);

    // 当有文件添加进来的时候
    uploader.on( 'beforeFileQueued', function( file ) {
        // $list为容器jQuery实例
        if(realconfig.fileNumLimit == 1){
            //单文件上传
            uploader.reset();
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        //console.log(file.source._refer.context.id);
        //console.log(file.source._refer.context.parentNode);

        $li = '<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '</h4>' +
            '<p class="state">等待上传...</p>' +
            '</div>';

        // $list为容器jQuery实例

        if(realconfig.fileNumLimit == 1){
            //单文件上传
            $('.uploader-list', file.source._refer.context.parentNode.parentNode).html( $li );
        }else {
            // $list为容器jQuery实例
            $('.uploader-list', file.source._refer.context.parentNode.parentNode).append( $li );
        }

        console.log(file);

    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress .progress-bar');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<div class="progress progress-striped active">' +
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
        }

        $li.find('p.state').text('上传中');

        $percent.css( 'width', percentage * 100 + '%' );
    });

    uploader.on( 'uploadSuccess', function( file, response ) {

        //console.log(file);
        //console.log(response);
        if(response.errNum != 0){//上传失败,返回错误信息
            $( '#'+file.id ).find('p.state').text(response.errMsg);
            return ;
        }
        $( '#'+file.id ).find('p.state').text('已上传');

        // 在制定容器里找需要的input
        var inputstr = "input[name='"+realconfig.fieldname+"']";
        var input = $(inputstr, $( '#'+file.id).parent().parent());

        //单张图片时
        if(realconfig.fileNumLimit == 1){
            input.val(file.name + ',' + response.retData.url+',' + response.retData.size);
        }else {
            var value = input.val();
            if (value) {
                //设置响应input(hidden)值
                input.val(value + ';' + file.name + ',' + response.retData.url + ',' + response.retData.size);
            } else {
                input.val(file.name + ',' + response.retData.url + ',' + response.retData.size);
            }
        }

    });

    uploader.on( 'uploadError', function( file ) {
        $( '#'+file.id ).find('p.state').text('上传出错');
    });

    uploader.on( 'uploadComplete', function( file, response ) {
        $( '#'+file.id ).find('.progress').fadeOut();
    });


    $('#uploadbtn'+realconfig.fieldname).click(function(){
        uploader.upload();
    })

}

function webupload_fileinit(config){

    var writeHtml = '<div class="uploader-container">'+
        '<div class="uploader-list"></div>'+
        '<div class="btns">'+
        '<div class="pick" id="pick'+config.fieldname+'">选择文件</div>'+
        '<div class="btn btn-default" style="float: left" id="uploadbtn'+config.fieldname+'">开始上传</div>'+
        '</div>'+
        '<div class="clear" style="clear: both;"></div>'+
        '<input type="hidden" name="'+config.fieldname+'" value="" autocomplete="off" class="inputfile" />'+
        '</div>';

    if('undefined' != typeof config.el){
        $(config.el).html(writeHtml);
    }else{
        document.writeln(writeHtml);
    }

    //避免重复加入
    for (var i in uploadFileArr){
        if(uploadFileArr[i].fieldname == config.fieldname)  return;
    }
    uploadFileArr.push($.extend({}, {pick:"#pick"+config.fieldname}, config));
}

//异步时这段需拷贝在需要的地方再执行
$(function(){
    for (var i in uploadFileArr){
        webuploadFiles(uploadFileArr[i]);
    }
})