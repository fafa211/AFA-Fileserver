<!DOCTYPE html>
<html lang="zh-CN">
<head id="Head1" runat="server">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>测试View4 - 异步上传文件</title>
    <link href="/static/bootstrap/css/bootstrap.css" rel="stylesheet">
    <script src="/static/js/libs/jquery.min.js"></script>


    <title></title>
    <script src="Scripts/jquery-1.3.2-vsdoc2.js" type="text/javascript"></script>
    <script type="text/javascript">

    var upload_token = "<?php echo $token;?>";
    var file_server = 'http://api.service.com/fileServer/uploadPic';
    var size_arr = "<?php echo json_encode(array(array(600,600),array(200,200)));?>";

        function uploadImage() {
            //判断是否有选择上传文件
            var imgPath = $("#uploadFile").val();
            if (imgPath == "") {
                alert("请选择上传图片！");
                return;
            }
            //判断上传文件的后缀名
            var strExtension = imgPath.substr(imgPath.lastIndexOf('.') + 1);
            if (strExtension != 'jpg' && strExtension != 'gif'
                && strExtension != 'png' && strExtension != 'bmp') {
                alert("请选择图片文件");
                return;
            }


            var formData = new FormData();
            formData.append('file', $('#uploadFile')[0].files[0]);
            formData.append('size', size_arr);
            $.ajax({
                url: file_server+'?token='+upload_token,
                type: 'POST',
                cache: false,
                data: formData,
                processData: false,
                contentType: false
            }).done(function(res) {
                alert("上传成功");
                $("#imgDiv").empty();
                $("#imgDiv").html(data);
                $("#imgDiv").show();
            }).fail(function(res) {

            });

//            $.ajax({
//                type: "POST",
//                url: "/hello/view4",
//                data: { imgPath: $("#uploadFile").val() },
//                cache: false,
//                success: function(data) {
//                    alert("上传成功");
//                    $("#imgDiv").empty();
//                    $("#imgDiv").html(data);
//                    $("#imgDiv").show();
//                },
//                error: function(XMLHttpRequest, textStatus, errorThrown) {
//                    alert("上传失败，请检查网络后重试");
//                }
//            });
        }
    </script>
</head>
<body>
<form  enctype="multipart/form-data" method="post">
    <input type="file" id="uploadFile" runat="server" />
    <input type="button" id="btnUpload" value="确定" onclick="uploadImage()" />
    <div id="imgDiv">
    </div>
</form>
</body>
</html>