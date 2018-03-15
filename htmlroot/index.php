<?php
/**
 * 统一入口文件
 */
//标志常量定义
define('AFA', true);

//版本号
define('VERSION', '1.0');

//调试状态, 0 不输出调试信息, 1输出到页面, 2输出到文件
define('DEBUG', 2);

//是否使用SWOOLE做服务器
define('USE_SWOOLE', false);

//打开代码提示
ini_set('display_errors', 'on');

//文件扩展名
define('EXT', '.php');

//视图模版文件扩展名
define('VIEW_EXT', '.php');

define('CHARSET', 'utf8');

//开始时间
define('AFA_START_TIME', microtime(true));
//开始内存大小
define('AFA_START_MEMORY', memory_get_usage());

//网站根目录所在路径
define('DOCROOT', dirname(__FILE__));
//项目所在目录路径
define('PROROOT', dirname(DOCROOT));
//system 目录
define('SYSTEMPATH', PROROOT.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR);
//system class 所在目录
define('CLASSPATH', SYSTEMPATH.'class'.DIRECTORY_SEPARATOR);
//application 应用所在目录
define('APPPATH', PROROOT.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
//modules 模块所在目录
define('MODULEPATH', PROROOT.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
//diver 驱动所在目录
define('DRIVERPATH',CLASSPATH.'driver'.DIRECTORY_SEPARATOR);

//默认控制器
define('DEFAULT_CONTROLLER', 'kingsum');
//默认Action
define('DEFAULT_ACTION', 'index');
//框架路由模式, 1为默认模式, 2为GET参数控制模式
define('ROUTE_MODE', 1);


//声明为全局变量
global $modules;

/**
 * 打开的模块设置
 */
$modules = array(
    'codemaker' => MODULEPATH.'codemaker'.DIRECTORY_SEPARATOR,//生成模块代码，正式生产环境下请删除此行
    //'blog' => MODULEPATH.'blog'.DIRECTORY_SEPARATOR,
    //'user' => MODULEPATH.'user'.DIRECTORY_SEPARATOR,
    'idcarea' => MODULEPATH.'idcarea'.DIRECTORY_SEPARATOR,
    'iparea' => MODULEPATH.'iparea'.DIRECTORY_SEPARATOR,
    'man' => MODULEPATH.'man'.DIRECTORY_SEPARATOR,
    'phonearea' => MODULEPATH.'phonearea'.DIRECTORY_SEPARATOR,
    'fileServer' => MODULEPATH.'fileServer'.DIRECTORY_SEPARATOR,
    'account' => MODULEPATH.'account'.DIRECTORY_SEPARATOR,//服务授权模块
    'logServer' => MODULEPATH.'logServer'.DIRECTORY_SEPARATOR,
    'article' => MODULEPATH.'article'.DIRECTORY_SEPARATOR,
    'page' => MODULEPATH.'page'.DIRECTORY_SEPARATOR,
    'leaveword' => MODULEPATH.'leaveword'.DIRECTORY_SEPARATOR,
    'multitable' => MODULEPATH.'multitable'.DIRECTORY_SEPARATOR,
    'sysaccount' => MODULEPATH.'sysaccount'.DIRECTORY_SEPARATOR,
    'modtest' => MODULEPATH.'modtest'.DIRECTORY_SEPARATOR,
    'rqs' => MODULEPATH.'rqs'.DIRECTORY_SEPARATOR,


);

require(CLASSPATH.'core'.EXT);
require(CLASSPATH.'db.php');
require(CLASSPATH.'F.php');

//声明为全局变量
global $config;

//载入配置文件
require 'config.php';

//执行请求
$request = Request::instance()->run();

//输出性能调试信息
F::debug($request);

?>