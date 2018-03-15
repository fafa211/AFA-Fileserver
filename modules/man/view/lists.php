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
    <h2>列表管理 phonearea</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>编号</th>
                <th>类名称</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($classArr as $k => $v): ?>
                <tr>
                        <?php
                                echo '<td>' . $k . '</td>';
                        ?>
                    <td><?php echo $v;?></td>
                    <td><a href="/man/sys/<?php echo $v; ?>" target="_blank">查看 <?php echo $v;?> 手册 &gt;</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
