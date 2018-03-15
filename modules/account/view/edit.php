<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>修改account</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
<script src="/static/js/libs/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
<h2>修改account</h2>
<form method='post' >
<div class="form-group">
<label name="account" for="inputaccount" class="control-label">账号</label>
<input type="text" name="account" class="form-control" id="inputaccount" placeholder="账号" value="<?php echo $account->account;?>" required>
</div>
<div class="form-group">
<label name="passwd" for="inputpasswd" class="control-label">密码</label>
<input type="password" name="passwd" class="form-control" id="inputpasswd" placeholder="密码" value="<?php echo $account->passwd;?>" required pattern="[0-9a-zA-z]{6,20}" title="密码必须为数字或字母，长度为6-20位">
</div>
<div class="form-group">
<label name="bindip" for="inputbindip" class="control-label">绑定服务器IP</label>
<input type="text" name="bindip" class="form-control" id="inputbindip" placeholder="绑定服务器IP" value="<?php echo $account->bindip;?>">
</div>
<button type="sumbit" class="btn btn-default">提交</button>
</form>
<a href="javascript:history.go(-1);">返回</a>
</div>
</body>
</html>
