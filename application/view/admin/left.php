<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>管理员登录</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
<script src="/static/js/libs/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
<h2>管理菜单</h2>

<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <a href="<?php echo $domain;?>account/lists" target="mainFrame">账号管理</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="<?php echo $domain;?>fileServer/fileinfo/lists" target="mainFrame">上传文件管理</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="<?php echo $domain;?>logServer/logtype/lists" target="mainFrame">日志服务管理</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="<?php echo $domain;?>account/logout" target="_top">退出</a>
                </td>
            </tr>
        </tbody>
    </table>

</div>



</div>
</body>
</html>
