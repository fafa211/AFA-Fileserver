<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>测试View</title>
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="/static/plugins/webuploader/webuploader.css" rel="stylesheet">
    <link href="/static/plugins/webuploader/webuploader_js_demo.css" rel="stylesheet">
    <script src="/static/js/libs/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
    <script src="/static/plugins/webuploader/js/webuploader.js"></script>

</head>
<body>


<div id="post-container" class="container">


    <div class="page-container">
        <h1 id="demo">Demo</h1>

        <p>您可以尝试文件拖拽，使用QQ截屏工具，然后激活窗口后粘贴，或者点击添加图片按钮，来体验此demo.</p>

        <div id="uploader" class="wu-example">
            <div class="queueList">
                <div id="dndArea" class="placeholder">
                    <div id="filePicker"></div>
                    <p>或将照片拖到这里，单次最多可选300张</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="progress">
                    <span class="text">0%</span>
                    <span class="percentage"></span>
                </div><div class="info"></div>
                <div class="btns">
                    <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    // 添加全局站点信息
    var BASE_URL = '/static/plugins/webuploader';
</script>
<script src="/static/plugins/webuploader/webuploader_js_demo.js"></script>

</body>
</html>