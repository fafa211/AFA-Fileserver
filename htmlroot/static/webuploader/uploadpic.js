/**
 * Created by zhengshufa on 17/7/29.
 * 基于jquery & webupload
 */

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

if("undefined" == typeof webupload_flash) var webupload_flash = $.webupload_path+'Uploader.swf';
if("undefined" == typeof webupload_imgserver) var webupload_imgserver = webupload_domain+'/fileServer/uploadPic?token=';
if("undefined" == typeof webupload_fileVal) var webupload_fileVal = 'myfile';
if("undefined" == typeof webupload_sizearr) var webupload_sizearr = JSON.stringify([[1600,1600],[600,600]]);

//默认配置
var webupload_img_config = {
    // 选完文件后，是否自动上传。
    auto: true,
    // swf文件路径
    swf: webupload_flash,
    // 文件接收服务端。
    server: webupload_imgserver,
    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '.uploader-container .pick',
    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,png',
        mimeTypes: 'image/*'
    },
    formData:{"sizearr":webupload_sizearr, "fileVal": webupload_fileVal},
    fileVal: webupload_fileVal, //上传文件input字段名称修改,配合文件服务器
    thumb:{width:120, height:120},//略缩图大小,可在此修改默认配置
    fileNumLimit:1,  //文件数量限制
    fileSingleSizeLimit:5*1024*1024, //单个文件大小
}

//上传的实体
var uploadImgArr = [];

function webuploadImgs(config){
    var realconfig = $.extend({}, webupload_img_config, config);
    realconfig.server = webupload_img_config.server+config.token;
    realconfig.formData.sizearr = config.sizearr;

    //console.log(config);
    console.log(realconfig);
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
        var $li = $(
                '<div id="' + file.id + '" class="file-item thumbnail">' +
                '<img>' +
                '<div class="info">' + file.name + '</div>' +
                '</div>'
            ),
            $img = $li.find('img');

        //console.log(file.source._refer.context.id);
        //console.log(file.source._refer.context.parentNode);

        if(realconfig.fileNumLimit == 1){
            //单张图片上传
            $('.uploader-list', file.source._refer.context.parentNode).html( $li );
        }else {
            // $list为容器jQuery实例
            $('.uploader-list', file.source._refer.context.parentNode).append($li);
        }

        // 创建缩略图
        // 如果为非图片文件，可以不用调用此方法。
        // thumbnailWidth x thumbnailHeight 为 100 x 100
        uploader.makeThumb( file, function( error, src ) {
            if ( error ) {
                $img.replaceWith('<span>不能预览</span>');
                return;
            }

            $img.attr( 'src', src );
        }, realconfig.thumb.width, realconfig.thumb.height );
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress span');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<p class="progress"><span></span></p>')
                .appendTo( $li )
                .find('span');
        }

        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file, response) {
        $( '#'+file.id ).addClass('upload-state-done');

        //在指定的容器里找需要的input
        var inputstr = "input[name='"+realconfig.fieldname+"']";
        var input = $(inputstr, $( '#'+file.id).parent().parent());

        //单张图片时
        if(realconfig.fileNumLimit == 1){
            input.val(file.name + ',' + response.retData.url);
        }else {
            var value = input.val();
            if (value) {
                //设置响应input(hidden)值
                input.val(value + ';' + file.name + ',' + response.retData.url);
            } else {
                input.val(file.name + ',' + response.retData.url);
            }
        }
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        var $li = $( '#'+file.id ),
            $error = $li.find('div.error');

        // 避免重复创建
        if ( !$error.length ) {
            $error = $('<div class="error"></div>').appendTo( $li );
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').remove();

        //重新刷新, 使上传按钮可再次使用
        uploader.refresh();
    });

}

function webupload_imginit(config){
    var writeHtml = '<div class="uploader-container">'+
        '<div class="uploader-list"></div>'+
        '<div class="pick" id="pick'+config.fieldname+'">选择图片</div>'+
        '<input type="hidden" name="'+config.fieldname+'" value="" autocomplete="off" class="inputfile" />'+
        '</div>';
    if('undefined' != typeof config.el){
        $(config.el).html(writeHtml);
    }else{
        document.writeln(writeHtml);
    }

    //避免重复加入
    for (var i in uploadImgArr){
        if(uploadImgArr[i].fieldname == config.fieldname)  return;
    }
    uploadImgArr.push($.extend({}, {pick:"#pick"+config.fieldname}, config));
}

$(function(){
    for (var i in uploadImgArr){
        webuploadImgs(uploadImgArr[i]);
    }
})