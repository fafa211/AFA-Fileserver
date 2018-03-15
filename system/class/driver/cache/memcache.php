<?php 
class Cache_Memcache extends cache{

	// Memcache has a maximum cache lifetime of 30 days
	const CACHE_CEILING = 2592000;

	protected $_memcache;

	protected $_config;
	
	protected $_flags;

	protected $_default_config = array(
		'host'             => '127.0.0.1',
		'port'             => 11211,
		'persistent'       => FALSE,
		'weight'           => 2,
		'timeout'          => 5,
		'retry_interval'   => 15,
		'status'           => TRUE,
		'instant_death'	   => TRUE,
		'lifetime'		   => 3600,
		'failure_callback' => array());

	public function _sanitize_id($id)
	{
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

	protected function __construct(array $config)
	{

		if ( ! extension_loaded('memcache'))
		{
			die('Memcache PHP extention not loaded');
		}
		parent::__construct($config);
		$this->_memcache = new Memcache;
		$server = $config;
		// Setup default server configuration
		$this->_default_config['failure_callback'] = array($this, '_failed_request');

		$server = $this->_config = array_merge($this->_default_config, $config);

		//$server =$this->_default_config;
		if(!$this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval'], $server['status'], $server['failure_callback']))
		{
			die('Memcache could not connect to host \':host\' using host:'.$server['host'].',    port:'.$server['port']);
		}
		$this->_flags = FALSE;
	}


	public function get($id, $default = NULL)
	{
		// Get the value from Memcache
		$value = $this->_memcache->get($this->_sanitize_id($id));

		// If the value wasn't found, normalise it
		if ($value === FALSE)
		{
			$value = (NULL === $default) ? NULL : $default;
		}

		// Return the value
		return $value;
	}


	public function set($id, $data, $lifetime = false)
	{
		if($lifetime === false){
			$lifetime = $this->_config['lifetime'];
		}
		// If the lifetime is greater than the ceiling
		if ($lifetime > Cache_Memcache::CACHE_CEILING)
		{
			// Set the lifetime to maximum cache time
			$lifetime = Cache_Memcache::CACHE_CEILING + time();
		}
		// Else if the lifetime is greater than zero
		elseif ($lifetime > 0)
		{
			$lifetime += time();
		}
		// Else
		else
		{
			// Normalise the lifetime
			$lifetime = 0;
		}
		// Set the data to memcache
		return $this->_memcache->set($this->_sanitize_id($id), $data, $this->_flags, $lifetime);
	}


	public function delete($id, $timeout = 0)
	{
		// Delete the id
		return $this->_memcache->delete($this->_sanitize_id($id), $timeout);
	}


	public function delete_all()
	{
		$result = $this->_memcache->flush();
		sleep(1);
		return $result;
	}


	public function _failed_request($hostname, $port)
	{
		if ( ! $this->_config['instant_death'])
		return;

		$host = FALSE;

		if ( ! $host)
		return;
		else
		{
			return $this->_memcache->setServerParams(
			$host['host'],
			$host['port'],
			$host['timeout'],
			$host['retry_interval'],
			FALSE, // Server is offline
			array($this, '_failed_request'
			));
		}
	}


	public function increment($id, $step = 1)
	{
		return $this->_memcache->increment($id, $step);
	}


	public function decrement($id, $step = 1)
	{
		return $this->_memcache->decrement($id, $step);
	}
}