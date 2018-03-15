<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>列表管理 account</title>
<link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
<script src="/static/js/libs/jquery.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>

 <style type="text/css">

  table{table-layout: fixed;}
  table td{
   word-break:break-all;
   word-wrap:break-word;
  }

 </style>
</head>
<body>
<div class="container">
<h2>列表管理 account</h2>
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th width="5%">ID</th>
<th>账号</th>
<th>密码</th>
<th width="30%">通讯秘钥</th>
<th>绑定服务器IP</th>
<th>登录ip</th>
<th>登录时间</th>
<th>注册时间</th>
<th>操作</th></tr>
</thead>
<tbody>
<?php foreach ($lists as $k=>$arr):?>
<tr>
<?php foreach ($arr as $k=>$v):?>
<?php if(in_array($k, $list_fields_arr)) {
$param = $k.'_arr';
if (isset($$param) && is_array($$param)){
echo '<td>'.F::findInArray($v, $$param).'</td>';
}else{
 echo '<td>'.$v.'</td>';}
}
?><?php endforeach;?>
<td><a href="show/<?php echo $arr['id'];?>">查看</a> <a href="edit/<?php echo $arr['id'];?>">修改</a> <a href="delete/<?php echo $arr['id'];?>">删除</a> </td></tr>
<?php endforeach;?>
</tbody>
</table>
</div>
<a href="add" class="col-md-1">新增</a> <a href="javascript:history.go(-1);">返回</a>
</div>
</body>
</html>
