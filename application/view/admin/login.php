<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="<?php echo X_UA_COMPATIBLE; ?>" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="blank" />
    <meta name="format-detection" content="telephone=no" />
    <title><?php echo $this->page_title; ?></title>

    <link type="text/css" rel="stylesheet" href="<?php echo G_STATIC_URL; ?>/css/bootstrap.css?v=<?php echo G_VERSION_BUILD; ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo G_STATIC_URL; ?>/css/icon.css?v=<?php echo G_VERSION_BUILD; ?>" />
    <script type="text/javascript">
        var G_INDEX_SCRIPT = "<?php echo G_INDEX_SCRIPT; ?>";
        var G_BASE_URL = "<?php echo input::uri('base'); ?>";
        var G_USER_ID = "<?php echo $this->user_id; ?>";
        var G_POST_HASH = "";
    </script>
    <?php if (is_array($this->_import_css_files)) { ?>
        <?php foreach ($this->_import_css_files AS $import_css) { ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $import_css; ?>?v=<?php echo G_VERSION_BUILD; ?>" />
        <?php } ?>
    <?php } ?>
    <?php if (is_array($this->_import_js_files)) { ?>
        <?php foreach ($this->_import_js_files AS $import_js) { ?>
            <script type="text/javascript" src="<?php echo $import_js; ?>?v=<?php echo G_VERSION_BUILD; ?>" ></script>
        <?php } ?>
    <?php } ?>
</head>

<body>
<div class="aw-login">
    <div class="mod center-block">
        <h1><img src="<?php echo G_STATIC_URL; ?>/admin/img/wecenter-logo.png" alt="" /></h1>

        <form role="form" id="login_form"  onsubmit="return false" action="/admin/login_process" method="post">
            <?php if (input::get('url')){ ?>
                <input type="hidden" name="url" value="<?php echo htmlspecialchars(input::get('url')); ?>">
            <?php } ?>

            <div class="alert alert-danger hide error_message"></div>

            <div class="form-group">
                <label>账号</label>
                <input type="text" name="account" class="form-control" placeholder="账号" value=""  />
                <i class="icon icon-user"></i>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input name="passwd" type="password" class="form-control" placeholder="密码" onkeydown="if (event.keyCode == 13) { document.getElementById('login_submit').click(); };" autofocus/>
                <i class="icon icon-lock"></i>
            </div>

            <div class="form-group">
                <label>验证码</label>
                <div class="row">
                    <div class="col-xs-5">
                        <input type="text" class="form-control" name="seccode_verify" onkeydown="if (event.keyCode == 13) { document.getElementById('login_submit').click(); };" maxlength="4" />
                    </div>
                    <div class="col-xs-4 col-xs-offset-1">
                        <img src="" class="verification" id="captcha" onclick="this.src =  G_BASE_URL + 'captcha/default?kn=' + Math.floor(Math.random() * 10000);" />
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary" id="login_submit" onclick="AWS.ajax_post($('#login_form'), AWS.ajax_processer, 'error_message');">登录</button>
        </form>

        <h2 class="text-center text-color-999">AFA Admin Control</h2>
    </div>
</div>
