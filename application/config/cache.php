<?php

//缓存设置
return array(
	'driver' => 'file',
	'file'=>array(
		'cache_dir' => PROROOT.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'cache',
		'default_expire' => 3600,
	),
	'memcache'=> array(
		'host'             => 'localhost',
		'port'             => 11211,
		'persistent'       => FALSE,
		'lifetime'         => 3600,  //缓存时间
	)
);