<?php
// 定义版本
define('G_VERSION_BUILD', '2.0');

// 定义 Cookies 作用域
define('G_COOKIE_DOMAIN','.afacms.com');

// 定义 Cookies 前缀
define('G_COOKIE_PREFIX','afa_');

// 定义应用加密 KEY
define('G_SECUKEY','npeytczyukfa');
define('G_COOKIE_HASH_KEY', 'rgkayzlcwokgiic');

define('G_INDEX_SCRIPT', '/');

define('X_UA_COMPATIBLE', 'IE=edge,Chrome=1');

// GZIP 压缩输出页面
define('G_GZIP_COMPRESS', FALSE);

// Session 存储类型 (db, file)
define('G_SESSION_SAVE', 'db');

// Session 文件存储路径
define('G_SESSION_SAVE_PATH', '');

//静态文件目录URL
define('G_STATIC_URL', '/static');


$config = array();

//主域名设置
$config['domain'] = "http://fs.afacms.com/";

//后缀，静态化使用,只能是 .html, .shtml, .php等常用脚本后缀，否则很容易出问题
$config['suffix'] = '.html';

//session配置
$config['session'] = array(
    'native'=>array(
        'name'=>'ssid',
        'lifetime'=>3600,
        'encrypted'=>false,
    )
);

//分页设置
$config['page'] = array(
    'psize' => 10, // 每页显示的记录数
    'pnum' => 5 // 页码偏移量
);

//上传设置
$config['upload'] = array(
    'direct' => DOCROOT.DIRECTORY_SEPARATOR.'upload',
    'size' => 2097152,
    'domain'=>"http://fs1.afacms.com/"
);

//加解密秘钥
$config['authkey'] = 'sj323TY#@w1&$qw21';

//数据库设置, 支持多数据库, 支持主从
$config['database'] = array(
    'default' => array(//默认数据库
        // 数据库设置
        'master' => array( // 数据库链接设置 Master
            'host' => 'localhost', // 数据库主机名或IP
            'user' => 'afacms', // 用户名
            'password' => '123456', // 密码
            'dbname' => 'afacms', // 数据库名称
            'charset' => 'utf8', // 字符集
            'conmode' => false
        ),
    ),
    'logServer' => array(//默认数据库
        // 数据库设置
        'master' => array( // 数据库链接设置 Master
            'host' => 'localhost', // 数据库主机名或IP
            'user' => 'afacms', // 用户名
            'password' => '123456', // 密码
            'dbname' => 'afacms', // 数据库名称
            'charset' => 'utf8', // 字符集
            'conmode' => false
        ),
    ),
    'sysaccount' => array(//默认数据库
        // 数据库设置
        'master' => array( // 数据库链接设置 Master
            'host' => 'localhost', // 数据库主机名或IP
            'user' => 'sys_account_dbs', // 用户名
            'password' => 'sys_account_dbs123', // 密码
            'dbname' => 'sys_account_dbs', // 数据库名称
            'charset' => 'utf8', // 字符集
            'conmode' => false
        ),
    ),

//    'multitable' => array(//默认数据库
//        // 数据库设置
//        'master' => array( // 数据库链接设置 Master
//            'host' => 'localhost', // 数据库主机名或IP
//            'user' => 'sys_account_dbs', // 用户名
//            'password' => 'sys_account_dbs123', // 密码
//            'dbname' => 'sys_account_dbs', // 数据库名称
//            'charset' => 'utf8', // 字符集
//            'conmode' => false
//        ),
//    ),




);// true为长久连接模式，false为短暂连接模式


$config['email'] = array(
    'driver'=>'smtp',       //验证方式
    'options'=>array(
        'hostname'=>'smtp.exmail.qq.com',     //邮件SMTP服务器地址
        'username'=>'zhengsf@kingsum.com.cn',     //邮件帐号
        'password'=>'Faaaaaa123',     //密码
        //'encryption'=>'',   //加密方式
        'from'=>'zhengsf@kingsum.com.cn',         //显示的发送邮件帐号
        'port'=>25,         //邮件服务器端口号
        'timeout'=>10,
        //'auth'=>''
    )
);
