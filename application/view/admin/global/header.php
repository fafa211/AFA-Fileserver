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
    <title><?php echo $page_title; ?></title>

    <link type="text/css" rel="stylesheet" href="<?php echo G_STATIC_URL; ?>/css/bootstrap.css?v=<?php echo G_VERSION_BUILD; ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo G_STATIC_URL; ?>/css/icon.css?v=<?php echo G_VERSION_BUILD; ?>" />
    <script type="text/javascript">
        var G_INDEX_SCRIPT = "<?php echo G_INDEX_SCRIPT; ?>";
        var G_BASE_URL = "<?php echo input::uri('base'); ?>";
        var G_STATIC_URL = "<?php echo G_STATIC_URL; ?>";
        var G_UPLOAD_URL = "<?php //echo get_setting('upload_url'); ?>";
        var G_USER_ID = "<?php echo $user; ?>";
        var G_POST_HASH = "";
    </script>
    <?php if (is_array($_import_css_files)) { ?>
        <?php foreach ($_import_css_files AS $import_css) { ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $import_css; ?>?v=<?php echo G_VERSION_BUILD; ?>" />
        <?php } ?>
    <?php } ?>
    <?php if (is_array($_import_js_files)) { ?>
        <?php foreach ($_import_js_files AS $import_js) { ?>
            <script type="text/javascript" src="<?php echo $import_js; ?>?v=<?php echo G_VERSION_BUILD; ?>" ></script>
        <?php } ?>
    <?php } ?>
    <!--[if lte IE 8]>
    <script type="text/javascript" src="<?php echo G_STATIC_URL; ?>/js/respond.js"></script>
    <![endif]-->
</head>

<body>
<div  class="aw-header">
    <button class="btn btn-sm mod-head-btn pull-left">
        <i class="icon icon-bar"></i>
    </button>

    <div class="mod-header-user">
        <ul class="pull-right">
            <?php $notifications_texts = array();//AWS_APP::model('admin')->get_notifications_texts(); ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle mod-bell" data-toggle="dropdown">
                    <i class="icon icon-bell"></i>
                    <?php if ($notifications_texts) { ?>
                        <span class="label label-danger"><?php echo count($notifications_texts); ?></span>
                    <?php } ?>
                </a>
                <ul class="dropdown-menu mod-chat">
                    <?php if ($notifications_texts) { ?>
                        <div class="mod-list-head">
                            <?php echo ('您有 '.count($notifications_texts).' 条消息') ; ?>
                        </div>

                        <?php foreach ($notifications_texts AS $notifications_text) { ?>
                            <li class="mod-media">
                                <a href="<?php echo $notifications_text['url']; ?>">
                                    <?php echo $notifications_text['text']; ?>
                                </a>
                            </li>
                        <?php } } else { ?>
                        <p><?php echo ('没有通知'); ?></p>
                    <?php } ?>
                </ul>
            </li>

            <li class="dropdown username">
                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?php //echo get_avatar_url($this->user_info['uid'], 'mid'); ?>" class="img-circle" width="30">
                    <?php echo $user; ?>
                    <span class="caret"></span>
                </a>

                <ul class="dropdown-menu pull-right mod-user">
                    <li>
                        <a href="<?php echo input::uri('base'); ?>" target="_blank"><i class="icon icon-home"></i><?php echo ('首页'); ?></a>
                    </li>

                    <li>
                        <a href="/admin/index"><i class="icon icon-ul"></i><?php echo ('概述'); ?></a>
                    </li>

                    <li>
                        <a href="/admin/logout/"><i class="icon icon-logout"></i><?php echo('退出'); ?></a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
