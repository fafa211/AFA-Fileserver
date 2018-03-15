<?php 
abstract class Cache {

	const DEFAULT_EXPIRE = 3600;

	/**
	 * @var   string     default driver to use
	 */
	public static $default = 'memcache';

	/**
	 * @var   Kohana_Cache instances
	 */
	public static $instances = array();

	/**
	 * @param   string   the name of the cache group to use [Optional]
	 * @return  Kohana_Cache
	 * @throws  Cache_Exception
	 */
	public static function instance($group = NULL)
	{
		$config = F::config('cache'.($group?'.'.$group:""));
		if($group === NULL){
			$group = $config["driver"];
		}
		
		$cache_class = 'Cache_'.ucfirst($config['driver']);
		if(!class_exists($cache_class, false)){
		    $file = F::find_file('driver', 'cache/'.$config["driver"]);
	   		$file && include $file;
		}
		if(isset($config[$group])) $config = $config[$group];
		
		Cache::$instances[$group] = new $cache_class($config);
		// Return the instance
		return Cache::$instances[$group];
	}

	/**
	 * @var  Config
	 */
	protected $_config = array();

	/**
	 * Ensures singleton pattern is observed, loads the default expiry
	 * 
	 * @param  array     configuration
	 */
	protected function __construct(array $config)
	{
		$this->config($config);
		$config = & $GLOBALS['config']['cache'];
	}

	/**
	 * @param   mixed    key to set to array, either array or config path
	 * @param   mixed    value to associate with key
	 * @return  mixed
	 */
	public function config($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_config;

		if (is_array($key))
		{
			$this->_config = $key;
		}
		else
		{
			if ($value === NULL)
				$this->_config = $key;

			$this->_config[$key] = $value;
		}
		return $this;
	}

	/**
	 * Overload the __clone() method to prevent cloning
	 *
	 * @return  void
	 * @throws  Cache_Exception
	 */
	final public function __clone()
	{
		throw new Exception('Cloning of Cache objects is forbidden', E_ERROR);
	}

	/**
	 * Retrieve a cached value entry by id.
	 * 
	 *     // Retrieve cache entry from default group
	 *     $data = Cache::instance()->get('foo');
	 * 
	 *     // Retrieve cache entry from default group and return 'bar' if miss
	 *     $data = Cache::instance()->get('foo', 'bar');
	 * 
	 *     // Retrieve cache entry from memcache group
	 *     $data = Cache::instance('memcache')->get('foo');
	 *
	 * @param   string   id of cache to entry
	 * @param   string   default value to return if cache miss
	 * @return  mixed
	 * @throws  Cache_Exception
	 */
	abstract public function get($id, $default = NULL);

	/**
	 * Set a value to cache with id and lifetime
	 * 
	 *     $data = 'bar';
	 * 
	 *     // Set 'bar' to 'foo' in default group, using default expiry
	 *     Cache::instance()->set('foo', $data);
	 * 
	 *     // Set 'bar' to 'foo' in default group for 30 seconds
	 *     Cache::instance()->set('foo', $data, 30);
	 * 
	 *     // Set 'bar' to 'foo' in memcache group for 10 minutes
	 *     if (Cache::instance('memcache')->set('foo', $data, 600))
	 *     {
	 *          // Cache was set successfully
	 *          return
	 *     }
	 *
	 * @param   string   id of cache entry
	 * @param   string   data to set to cache
	 * @param   integer  lifetime in seconds
	 * @return  boolean
	 */
	abstract public function set($id, $data, $lifetime = 3600);

	/**
	 * Delete a cache entry based on id
	 * 
	 *     // Delete 'foo' entry from the default group
	 *     Cache::instance()->delete('foo');
	 * 
	 *     // Delete 'foo' entry from the memcache group
	 *     Cache::instance('memcache')->delete('foo')
	 *
	 * @param   string   id to remove from cache
	 * @return  boolean
	 */
	abstract public function delete($id);

	/**
	 * Delete all cache entries.
	 * 
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 * 
	 *     // Delete all cache entries in the default group
	 *     Cache::instance()->delete_all();
	 * 
	 *     // Delete all cache entries in the memcache group
	 *     Cache::instance('memcache')->delete_all();
	 *
	 * @return  boolean
	 */
	abstract public function delete_all();

	/**
	 * Replaces troublesome characters with underscores.
	 *
	 *     // Sanitize a cache id
	 *     $id = $this->_sanitize_id($id);
	 * 
	 * @param   string   id of cache to sanitize
	 * @return  string
	 */
	protected function _sanitize_id($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(array('/', '\\', ' '), '_', $id);
	}
}
// End Kohana_Cache
