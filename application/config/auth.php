<?php

return array(

	'driver'       => 'file',
//	'driver'       => 'sysaccount',
	'hash_method'  => 'sha256',
	'hash_key'     => '_fa321!@#',
	'lifetime'     => 1209600,
	'session_type' => Session::$default,
	'session_key'  => 'auth_user',
	'session_user'  => '__session_user',

	// Username/password combinations for the Auth File driver
	'users' => array(
		'admin' => 'd1938506828d449bb1ff1b727807bee856014ac2d57718289f679cfba5555f2f',
	),

);