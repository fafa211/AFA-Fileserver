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



<?php

Request::instance('hello/head')->run();

//echo $name;
//echo '<br />';
//echo $value;
?>

<div class="container">
    <h2>文件上传</h2>
    <form method='post' enctype="application/x-www-form-urlencoded">
        <div class="form-group">
            <label name="city" for="inputcity" class="control-label">文件</label>
            <input type="file" name="myFile" class="form-control" id="inputcity" placeholder="城市" value="" required>
        </div>
        <button type="sumbit" class="btn btn-default">提交</button>
    </form>
    <a href="javascript:history.go(-1);">返回</a>
</div>

<p><img src="<?php echo $api_domain;?>fileServer/showImage/<?php echo $hash_id_image;?>?token=<?php echo $token;?>" /> </p>
<p><a href="<?php echo $api_domain;?>fileServer/download/<?php echo $hash_id;?>?token=<?php echo $token;?>" target="_blank">下载文件</a> </p>

<?php

Request::instance('hello/foot')->run();

?>


</body>
</html>

