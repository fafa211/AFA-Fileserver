<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>类 <?php echo $class;?> 手册</title>
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/static/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <script src="/static/js/libs/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
</head>
<body>
<div class="container">
    <h2>类 <?php echo $class;?> 手册</h2>
    <div class="table-responsive">
        <?php foreach ($methodArr as $k => $v): ?>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td width="30%"><strong>类名称</strong></td>
                    <td><?php echo $v['class_name'];?></td>
                </tr>
                <tr>
                    <td><strong>方法名称</strong></td>
                    <td><?php echo $v['method_name'];?></td>
                </tr>
                <tr>
                    <td><strong>方法说明</strong></td>
                    <td><?php
                        if(!empty($v['method_description'])) {
                            $desc_arr = explode('//', $v['method_description']);
                            foreach ($desc_arr as $dv) {
                                echo '//' . $dv;
                                echo '<br >';
                            }
                        }else{
                            echo '无说明';
                        }
                        ?></td>
                </tr>
                <tr>
                    <td><strong>文件路径</strong></td>
                    <td><?php echo $v['file_name'];?></td>
                </tr>
                <tr>
                    <td><strong>方法所在起始行号</strong></td>
                    <td><?php echo $v['start_line'];?></td>
                </tr>
                <tr>
                    <td><strong>方法属性</strong></td>
                    <td><?php echo $v['method_type'];?></td>
                </tr>
                <tr>
                    <td><strong>方法参数</strong></td>
                    <td><?php if($v['param_count'] >= 1){
                            echo '共计 '.$v['param_count'].' 个参数<br/>';
                            foreach($v['param_arr'] as $subv){
                                echo '参数变量名称: '.$subv['name'].', 默认值: '.$subv['default'].', 参数说明: '.$subv['description'].'<br />';
                            }

                        }else{
                            echo '无参数';
                        } ?></td>
                </tr>
                <tr>
                    <td><strong>方法返回值</strong></td>
                    <td><?php echo $v['return'];?></td>
                </tr>
            </tbody>
        </table>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
