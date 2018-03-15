<?php
/**
 * Sysaccount Auth driver.
 * [!!] this Auth driver does not support roles nor autologin.
 *
 * @package    class
 * @category   auth
 * @author		shufa.zheng<22575353@qq.com>
 * @copyright	(c) 2015-2017 afaphp.com
 * @license		https://afaphp.com/license.html
 */

class Auth_Sysaccount extends Auth {

	// User list
	//protected $_users;

	protected $_config_type = 'sysuser';

	//当前用户名
	protected $_username;

	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Load user list
		//$this->_users = Arr::get($config, 'users', array());
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   $username  Username
	 * @param   string   $password  Password
	 * @param   boolean  $remember  Enable autologin (not supported)
	 * @return  boolean
	 */
	protected function _login($username, $password, $remember)
	{
		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		$this->_username = $username;
		$sysaccount = new Sysaccount_api2_Service($this->_config_type, true);
		$user = $sysaccount->getByAccount($username);

		if(!isset($user->passwd)) return false;

		if ($user->passwd == $password)
		{
			// Complete the login
			$this->complete_login($username);
			Session::instance()->set($this->_config['session_user'], $user);
			return true;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    $username  Username
	 * @return  boolean
	 */
	public function force_login($username)
	{
		// Complete the login
		return $this->complete_login($username);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   $username  Username
	 * @return  string
	 */
	public function password($username)
	{
		$sysaccount = new Sysaccount_api2_Service($this->_config_type, true);
		$user = $sysaccount->getByAccount($username);

		$this->_username = $username;

		return isset($user->passwd)?$user->passwd:'';
	}

	/**
	 * Compare password with original (plain text). Works for current (logged in) user
	 *
	 * @param   string   $password  Password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$sysaccount = new Sysaccount_api2_Service($this->_config_type, true);
		$user = $sysaccount->getByAccount($this->_username);

		if (empty($user))
		{
			return FALSE;
		}

		return ($password === $user->passwd);
	}

} // End Auth File
