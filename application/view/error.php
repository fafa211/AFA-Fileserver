<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>错误提示页面</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">

</head>
<body>
<?php if (DEBUG):?>
<div class="container">
<h2><?php echo $type;?>: <?php echo $message;?></h2>

<h3>错误位置为 文件 <?php echo $file;?> <br />  的第 <?php echo $line;?> 行</h3>

<h3>执行路径详情如下：</h3>
<div class="content lead">
<?php echo $trace;?>
</div>
</div>
<?php else:?>
<div class="container">
<h2><?php echo $type;?>: <?php echo $message;?></h2>
<h3>本地方可自由定义错误展示信息</h3>
</div>
<?php endif;?>
</body>
</html>
