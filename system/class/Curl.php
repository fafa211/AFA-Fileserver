<?php 

/**
 * Create instance of Curl 
 * $query = Curl::instance('config_entry');
 * 
 * or even simply put parameters
 * $opts = array(OPTION1 => $value1, OPTION2 => $value2);
 * $query = Curl::instance($opts); 
 * 
 * then we can set some additional options
 * 
 * $query
 *     ->set_opt(OPTION, $value)
 *     ->set_opt(ANOTHER_OPTION, $another_value);
 * 
 * or do it using array
 * 
 * $query->set_opts($opts);
 * 
 * and execute stuff
 *  
 * $result = $query->execute();
 *
 * @package Curl
 * @author Matt Wells
 * @author Alexander Kupreyeu (Kupreev) (http://kupreev.com)
 **/
class Curl {
    
    protected $instance = NULL;
    
    /**
     * Factory Method
     * @param   array  $config options array (will be merged with default entry)   
     * @return  object  new Curl object
     */
    public static function instance($config = NULL)
    {
        return new Curl($config);
    }
    
    /**
     * Constructor
     * @param   string|array  $config_entry name of config entry or options array (will be merged with default entry)   
     */
    public function __construct($config = NULL)
    {
        $config_arr = F::config('curl');
        
        $this->instance = curl_init();

        if (is_array($config))
        {
            $config_arr =  $config + $config_arr;
        }

        curl_setopt_array($this->instance, $config_arr);
        
    }
                     
    /**
     * Set option
     * @param string	$key	Curl option to set
     * @param string    $value	Value for option
     * @return  object  Curl
     */
    public function set_opt($key, $value)
    {
        curl_setopt($this->instance, $key, $value);
        
        return $this;
    }
    
    /**
     * Set options from array
     * @param   array   $options    array of options
     * @return  object  Curl 
     */
    public function set_opts($options)
    {
        curl_setopt_array($this->instance, $options);
        
        return $this;
    }
                     
    /**
     * Execute the curl request and return the response
     * @return string   Returned output from the requested resource
     * @throws Kohana_Exception
     */
    public function execute()
    {
        $result = curl_exec($this->instance);
        
        //Wrap the error reporting in an exception
        if ($result === FALSE)
        { 
            throw new Exception("Curl error: ".ucfirst(curl_error($this->instance)));
        }
        else
        {
            return $result;
        }
    }
    
    /**
     * Get error
     * Returns any current error for the curl request
     * @return  string  The error
     */
    public function get_error()
    {
        return curl_error($this->instance);
    }
    
    /**
     * Destructor
     */
    function __destruct()
    {
        curl_close($this->instance);
    }
    
    
    /**
     * Get
     * Execute an HTTP GET request using curl
     * @param   string  $url    url to request
     * @param   array   $headers    additional headers to send in the request
     * @param   bool    $headers_only   flag to return only the headers
     * @param   array   $curl_options   Additional curl options to instantiate curl with
     * @return  string  result
     */
    public static function get($url, Array $headers = array(), $headers_only = FALSE, Array $curl_options = array())
    {
        $ch = Curl::instance($curl_options);
        
        $ch->set_opt(CURLOPT_URL, $url)
            ->set_opt(CURLOPT_RETURNTRANSFER, TRUE)
            ->set_opt(CURLOPT_NOBODY, $headers_only);
        
        // Set any additional headers
        if( ! empty($headers))
        {
            $ch->set_opt(CURLOPT_HTTPHEADER, $headers);    
        } 
        
        return $ch->execute();
    }
    
    
    /**
     * Post
     * Execute an HTTP POST request, posting the past parameters
     * @param   string  $url    url to request
     * @param   array   $data   past data to post to $url
     * @param   array   $headers    additional headers to send in the request
     * @param   bool    $headers_only   flag to return only the headers
     * @param   array   $curl_options   additional curl options to instantiate curl with
     * @return  string  result 
     */
    public static function post($url, Array $data = array(), Array $headers = array(), $headers_only = FALSE, Array $curl_options = array())
    {
        $ch = Curl::instance($curl_options);
        
        $ch->set_opt(CURLOPT_URL, $url)
            ->set_opt(CURLOPT_NOBODY, $headers_only)
            ->set_opt(CURLOPT_RETURNTRANSFER, TRUE)
            ->set_opt(CURLOPT_POST, TRUE)
            ->set_opt(CURLOPT_POSTFIELDS, $data);
      
          //Set any additional headers
        if( ! empty($headers)) 
        {
            $ch->set_opt(CURLOPT_HTTPHEADER, $headers);
        }
        
        return $ch->execute();
    }
} // End Curl class