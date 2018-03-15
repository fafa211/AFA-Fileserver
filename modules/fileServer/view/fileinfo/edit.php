<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>修改fileinfo</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
<script src="/static/js/libs/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
<h2>修改fileinfo</h2>
<form method='post' >
<div class="form-group">
<label name="file_name" for="inputfile_name" class="control-label">文件名</label>
<input type="text" name="file_name" class="form-control" id="inputfile_name" placeholder="文件名" value="<?php echo $fileinfo->file_name;?>" required>
</div>
<div class="form-group">
<label name="file_type" for="inputfile_type" class="control-label">文件类型</label>
<input type="text" name="file_type" class="form-control" id="inputfile_type" placeholder="文件类型" value="<?php echo $fileinfo->file_type;?>" required>
</div>
<button type="sumbit" class="btn btn-default">提交</button>
</form>
<a href="javascript:history.go(-1);">返回</a>
</div>
</body>
</html>
