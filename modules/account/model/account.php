<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * account模型
* @author zhengshufa
* @date 2016-08-26 16:29:46
 */
class account_Model extends Model{

protected $module = 'account';
protected $table = 'account';
protected $primary = 'id';
protected $fileds = array(
'id'=>0,
'account'=>'',
'passwd'=>'1',
'keyno'=>'1',
'bindip'=>'127.0.0.1',
'loginip'=>'127.0.0.1',
'logintime'=>'0000-00-00 00:00:00',
'regtime'=>'0000-00-00 00:00:00',
);
}
