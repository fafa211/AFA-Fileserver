<?php 
/**
 * Cookie-based session class.
 *
 * @package    class
 * @category   Session
 * @author		shufa.zheng<22575353@qq.com>
 * @copyright	(c) 2015-2016 afaphp.com
 * @license		https://afaphp.com/license.html
 */
class Session_Cookie extends Session {

	/**
	 * @param   string  $id  session id
	 * @return  string
	 */
	protected function _read($id = NULL)
	{
		return input::cookie($this->_name);
	}

	/**
	 * @return  null
	 */
	protected function _regenerate()
	{
		// Cookie sessions have no id
		return NULL;
	}

	/**
	 * @return  bool
	 */
	protected function _write()
	{
		return input::cookie($this->_name, $this->__toString(), $this->_lifetime);
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _destroy()
	{
		return input::cookie($this->_name, null);
	}

} // End Session_Cookie
