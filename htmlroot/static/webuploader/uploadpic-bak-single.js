/**
 * Created by zhengshufa on 17/7/29.
 */

if("undefined" == typeof uploaderToken) var uploaderToken = '';
if("undefined" == typeof webupload_flash) var webupload_flash = '/static/webuploader/Uploader.swf';
if("undefined" == typeof webupload_filserver) var webupload_filserver = '/fileServer/uploadPic?token='+uploaderToken;
if("undefined" == typeof thumbnailWidth) var thumbnailWidth = 120;
if("undefined" == typeof thumbnailHeight) var thumbnailHeight = 120;
if("undefined" == typeof webupload_fileVal) var webupload_fileVal = 'myfile';
if("undefined" == typeof webupload_sizearr) var webupload_sizearr = JSON.stringify([[1600,1600],[600,600]]);

// 初始化Web Uploader
var uploaderImg = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: webupload_flash,

    // 文件接收服务端。
    server: webupload_filserver,

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

    fileVal: webupload_fileVal,

});


// 当有文件添加进来的时候
uploaderImg.on( 'fileQueued', function( file ) {
    var $li = $(
            '<div id="' + file.id + '" class="file-item thumbnail">' +
            '<img>' +
            '<div class="info">' + file.name + '</div>' +
            '</div>'
        ),
        $img = $li.find('img');

    //console.log(file.source._refer.context.id);
    //console.log(file.source._refer.context.parentNode);

    // $list为容器jQuery实例
    $('.uploader-list', file.source._refer.context.parentNode).append( $li );

    // 创建缩略图
    // 如果为非图片文件，可以不用调用此方法。
    // thumbnailWidth x thumbnailHeight 为 100 x 100
    uploaderImg.makeThumb( file, function( error, src ) {
        if ( error ) {
            $img.replaceWith('<span>不能预览</span>');
            return;
        }

        $img.attr( 'src', src );
    }, thumbnailWidth, thumbnailHeight );
});


// 文件上传过程中创建进度条实时显示。
uploaderImg.on( 'uploadProgress', function( file, percentage ) {
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
uploaderImg.on( 'uploadSuccess', function( file, response) {
    $( '#'+file.id ).addClass('upload-state-done');

    // $list为容器jQuery实例
    var value = $('.inputfile', file.source._refer.context.parentNode).val();
    console.log(value);
    if(value) {
        //设置响应input(hidden)值
        $('.inputfile', file.source._refer.context.parentNode).val(value+';'+file.name + ',' + response.retData.url);
    }else{
        $('.inputfile', file.source._refer.context.parentNode).val(file.name + ',' + response.retData.url);
    }
});

// 文件上传失败，显示上传出错。
uploaderImg.on( 'uploadError', function( file ) {
    var $li = $( '#'+file.id ),
        $error = $li.find('div.error');

    // 避免重复创建
    if ( !$error.length ) {
        $error = $('<div class="error"></div>').appendTo( $li );
    }

    $error.text('上传失败');
});

// 完成上传完了，成功或者失败，先删除进度条。
uploaderImg.on( 'uploadComplete', function( file ) {
    $( '#'+file.id ).find('.progress').remove();

});
