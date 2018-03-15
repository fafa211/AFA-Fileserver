<?php 

class Cache_Apc extends cache{


	public function _sanitize_id($id)
	{
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

	public function __construct(array $config)
	{
		if ( ! extension_loaded('apc'))
		{
			die('PHP APC extension is not available.');
		}
	}


	public function get($id, $default = NULL)
	{
		$data = apc_fetch($this->_sanitize_id($id), $success);

		return $success ? $data : $default;
	}


	public function set($id, $data, $lifetime = NULL)
	{
		if ($lifetime === NULL)
		{
			$lifetime = 3600;
		}

		return apc_store($this->_sanitize_id($id), $data, $lifetime);
	}


	public function delete($id)
	{
		return apc_delete($this->_sanitize_id($id));
	}


	public function delete_all()
	{
		return apc_clear_cache('user');
	}


	public function increment($id, $step = 1)
	{
		return apc_inc($id, $step);
	}


	public function decrement($id, $step = 1)
	{
		return apc_dec($id, $step);
	}

} // End Kohana_Cache_Apc