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
    <script src="/static/webuploader/uploadpic.js"></script>
    <script type="text/javascript">
        var uploaderToken = "<?php echo $token;?>";
    </script>
</head>
<body>

<script type="text/javascript">
    webupload_imginit({token:uploaderToken, sizearr:JSON.stringify([[600,600],[100,100]]), fieldname:"file1"});
    webupload_imginit({token:uploaderToken, sizearr:JSON.stringify([[300,300]]), fieldname:"file2"});
</script>


</body>
</html>