<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>展示fileinfo</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
<script src="/static/js/libs/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
<h2>展示fileinfo</h2>
<p><strong>帐号ID: </strong><?php echo $fileinfo->account_id;?></p>
<p><strong>文件名: </strong><?php echo $fileinfo->file_name;?></p>
<p><strong>文件类型: </strong><?php echo $fileinfo->file_type;?></p>
<p><strong>文件大小: </strong><?php echo $fileinfo->file_size;?></p>
<p><strong>创建时间: </strong><?php echo $fileinfo->add_time;?></p>
<p><strong>唯一HASH值: </strong><?php echo $fileinfo->hash_id;?></p>
<p><strong>文件后缀名: </strong><?php echo $fileinfo->suffix;?></p>
<a href="javascript:history.go(-1);">返回</a>
</div>
</body>
</html>
