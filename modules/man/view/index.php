<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>列表管理 phonearea</title>
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <script src="/static/js/libs/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
    <h2>AFAPHP开发框架介绍</h2>
    <div class="table-responsive">
        <h3>AFA-PHP Framework</h3>

        <p>AFA-PHP 是一个简单, 快速,  实用的PHP开发框架.</p>

        <p>AFAPHP有以下特性<br>
        1. 支持HMVC模式<br>
        2. MYSQL PDO, 支持一主多从主(读写分离)，支持按模块配置不同数据库，并且每个模块都可以进行一主多从<br>
        3. SQL build：SQL生成，可有效防止SQL注入<br>
        4. Modules：按模块进行项目开发，适合团队协作开发<br>
        5. Codemaker：自动生成模块代码，包括增,删,改,查(列表，单条记录展示)<br>
        6. 使用jquery,bootstrap<br>
        7. 代码自动加载<br>
        8. 支持不同模块间相互调用，支持自动与收到加载其他模块文件<br>
        9. 可内部 Request::instance('/hello/index')->run(); 自由调用，方便Controller自由交互<br>
            10. 完善的手册支持<br>
            <a href="lists">&gt;查看核心类手册</a>
        </p>
    </div>
</div>
</body>
</html>
