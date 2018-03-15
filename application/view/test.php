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
</head>
<body>

<h3>it is ok!</h3>
<p>
    <?php echo $name;?>
</p>

<p>
    <?php echo $get;?>
</p>

<p>
    <?php echo $value;?>
</p>

<p><?php Request::instance('hello/head')->run();?></p>

<p><?php echo $mysession;?></p>

</body>
</html>