<?php 
/**
 * Upload helper class for working with uploaded files and [Validation].
 *
 *     $array = Validation::factory($_FILES);
 *
 * [!!] Remember to define your form with "enctype=multipart/form-data" or file
 * uploading will not work!
 *
 * The following configuration properties can be set:
 *
 * - [Upload::$remove_spaces]
 * - [Upload::$default_directory]
 *
 * @package    class
 * @category   upload
 * @author     FROM Kohana Team
 */
class Upload {

	/**
	 * @var  boolean  remove spaces in uploaded files
	 */
	public static $remove_spaces = TRUE;

	/**
	 * @var  string  default upload directory
	 */
	public static $default_directory = 'upload';

    /**
     * @var 图片文件类型
     */
    public static $image_types = array(
        'image/png',
        'image/jpg',
        'image/jpeg',
        'image/gif'
    );

    public static $file_types = array(
        'txt','doc','pdf','rar','zip','png','jpg','gif','xls','xlsx','doc','docx','ppt','pptx','cvs','tar','gz','mp4','mp3','avi','amr','m4a'
    );

	/**
	 * Save an uploaded file to a new location. If no filename is provided,
	 * the original filename will be used, with a unique prefix added.
	 *
	 * This method should be used after validating the $_FILES array:
	 *
	 *     if ($array->check())
	 *     {
	 *         // Upload is valid, save it
	 *         Upload::save($array['file']);
	 *     }
	 *
	 * @param   array    uploaded file data
	 * @param   string   new filename
	 * @param   string   new directory
	 * @param   integer  chmod mask
	 * @return  string   on success, full path to new file
	 * @return  FALSE    on failure
	 */
	public static function save(array $file, $filename = NULL, $directory = NULL, $chmod = 0644)
	{
		if ( ! isset($file['tmp_name']) OR ! is_uploaded_file($file['tmp_name']))
		{
			// Ignore corrupted uploads
			return FALSE;
		}

		if ($filename === NULL)
		{
            // Use the default filename, with a timestamp pre-pended
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($file['type'], self::$image_types)) {
                $filename = uniqid('sc_') . '.' . $ext;
            } else {
                $filename = uniqid() . $file['name'];
            }
		}

		if (Upload::$remove_spaces === TRUE)
		{
			// Remove spaces from the filename
			$filename = preg_replace('/\s+/u', '_', $filename);
		}

		if ($directory === NULL)
		{
            // Use the pre-configured upload directory
            $config = F::config('upload');
            if ($config) {
                $directory = $config['direct'];
            } else {
                $directory = DOCROOT . DIRECTORY_SEPARATOR . Upload::$default_directory;
            }
        }
		    

		if ( ! is_dir($directory) OR ! is_writable(realpath($directory)))
		{
			throw new Exception('Directory '.$directory.' must be writable', E_ERROR);
		}

		$date = date('Ymd');
        $directory .= DIRECTORY_SEPARATOR . $date;
        if (! is_dir($directory)) {
            @mkdir($directory, 0755);
        }
		    
		// Make the filename into a complete path
		$filename = realpath($directory).DIRECTORY_SEPARATOR.$filename;

		if (move_uploaded_file($file['tmp_name'], $filename))
		{
			if ($chmod !== FALSE)
			{
				// Set permissions on filename
				chmod($filename, $chmod);
			}

			// Return new file path
			return $filename;
		}

		return FALSE;
	}

	/**
	 * Save an uploaded file to a new location. If no filename is provided,
	 * the original filename will be used, with a unique prefix added.
	 *
	 * This method should be used after validating the $_FILES array:
	 *
	 *     if ($array->check())
	 *     {
	 *         // Upload is valid, save it
	 *         Upload::save($array['file']);
	 *     }
	 *
	 * @param   array    uploaded file data
	 * @return  FALSE    on failure
	 */
	public static function saveHash(array $file)
	{
		if ( ! isset($file['tmp_name']) OR ! is_uploaded_file($file['tmp_name']))
		{
			// Ignore corrupted uploads
			return FALSE;
		}

        $save_info = self::getSaveInfo($file);

		if ($save_info !== false && move_uploaded_file($file['tmp_name'], $save_info['filename']))
		{
			// Return new file path
			return $save_info;
		}

		return FALSE;
	}

    /**
     * @param $file
     * @return array|bool
     * @throws Exception
     */
	public static function getSaveInfo($file){
        //文件唯一标识ID
        $hash_id = md5($file['tmp_name'].'-'.uniqid());

        // Use the default filename, with a timestamp pre-pended
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext, self::$file_types)) return false;

        //文件名称
        $filename = $hash_id . '.' . $ext;
        //文件存储目录
        $directory = self::getDirectory($hash_id);
        //创建文件存储目录
        $flag = common::createDir($directory);
        if(!$flag) {
            throw new Exception('Directory '.$directory.' must be writable', E_ERROR);
            return false;
        }

        // Make the filename into a complete path
        $filename = $directory.DIRECTORY_SEPARATOR.$filename;

        return array(
            'hash_id'=>$hash_id,
            'suffix'=>$ext,
            'filename'=>$filename
        );
    }

    /**
     * 生成一个随机的文件信息
     * @return array
     * @throws Exception
     */
	public static function createRandFile(){
        //文件唯一标识ID
        $hash_id = md5(uniqid());

        //文件名称
        $filename = $hash_id;
        //文件存储目录
        $directory = self::getDirectory($hash_id);
        //创建文件存储目录
        $flag = common::createDir($directory);
        if(!$flag) {
            throw new Exception('Directory '.$directory.' must be writable', E_ERROR);
            return false;
        }

        // Make the filename into a complete path
        $filename = $directory.DIRECTORY_SEPARATOR.$filename;

        return array(
            'hash_id'=>$hash_id,
            'filename'=>$filename
        );
    }

    /**
     * @param $hash_id 文件唯一hash_id
     */
    public static function getDirectory($hash_id){
        $config = F::config('upload');
        if ($config) {
            $directory = $config['direct'];
        } else {
            $directory = DOCROOT . DIRECTORY_SEPARATOR . Upload::$default_directory;
        }

        $directory .= DIRECTORY_SEPARATOR . substr($hash_id, 0, 2).DIRECTORY_SEPARATOR.substr($hash_id, 2, 2);
        return $directory;
    }

	/**
	 * Tests if upload data is valid, even if no file was uploaded. If you
	 * _do_ require a file to be uploaded, add the [Upload::not_empty] rule
	 * before this rule.
	 *
	 *     $array->rule('file', 'Upload::valid')
	 *
	 * @param   array  $_FILES item
	 * @return  bool
	 */
	public static function valid($file)
	{
		return (isset($file['error'])
			AND isset($file['name'])
			AND isset($file['type'])
			AND isset($file['tmp_name'])
			AND isset($file['size']));
	}

	/**
	 * Tests if a successful upload has been made.
	 *
	 *     $array->rule('file', 'Upload::not_empty');
	 *
	 * @param   array    $_FILES item
	 * @return  bool
	 */
	public static function not_empty(array $file)
	{
		return (isset($file['error'])
			AND isset($file['tmp_name'])
			AND $file['error'] === UPLOAD_ERR_OK
			AND is_uploaded_file($file['tmp_name']));
	}

	/**
	 * Test if an uploaded file is an allowed file type, by extension.
	 *
	 *     $array->rule('file', 'Upload::type', array(':value', array('jpg', 'png', 'gif')));
	 *
	 * @param   array    $_FILES item
	 * @param   array    allowed file extensions
	 * @return  bool
	 */
	public static function type(array $file, array $allowed)
	{
		if ($file['error'] !== UPLOAD_ERR_OK)
			return TRUE;

		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		return in_array($ext, $allowed);
	}

	/**
	 * Validation rule to test if an uploaded file is allowed by file size.
	 * File sizes are defined as: SB, where S is the size (1, 8.5, 300, etc.)
	 * and B is the byte unit (K, MiB, GB, etc.). All valid byte units are
	 * defined in Num::$byte_units
	 *
	 *     $array->rule('file', 'Upload::size', array(':value', '1M'))
	 *     $array->rule('file', 'Upload::size', array(':value', '2.5KiB'))
	 *
	 * @param   array    $_FILES item
	 * @param   string   maximum file size allowed
	 * @return  bool
	 */
	public static function size(array $file, $size)
	{
		if ($file['error'] === UPLOAD_ERR_INI_SIZE)
		{
			// Upload is larger than PHP allowed size (upload_max_filesize)
			return FALSE;
		}

		if ($file['error'] !== UPLOAD_ERR_OK)
		{
			// The upload failed, no size to check
			return TRUE;
		}

		// Convert the provided size to bytes for comparison
		$size = Num::bytes($size);

		// Test that the file is under or equal to the max size
		return ($file['size'] <= $size);
	}

	/**
	 * Validation rule to test if an upload is an image and, optionally, is the correct size.
	 *
	 *     // The "image" file must be an image
	 *     $array->rule('image', 'Upload::image')
	 *
	 *     // The "photo" file has a maximum size of 640x480 pixels
	 *     $array->rule('photo', 'Upload::image', array(640, 480));
	 *
	 *     // The "image" file must be exactly 100x100 pixels
	 *     $array->rule('image', 'Upload::image', array(100, 100, TRUE));
	 *
	 *
	 * @param   array    $_FILES item
	 * @param   integer  maximum width of image
	 * @param   integer  maximum height of image
	 * @param   boolean  match width and height exactly?
	 * @return  boolean
	 */
	public static function image(array $file, $max_width = NULL, $max_height = NULL, $exact = FALSE)
	{
		if (Upload::not_empty($file))
		{
			try
			{
				// Get the width and height from the uploaded image
				list($width, $height) = getimagesize($file['tmp_name']);
			}
			catch (ErrorException $e)
			{
				// Ignore read errors
			}

			if (empty($width) OR empty($height))
			{
				// Cannot get image size, cannot validate
				return FALSE;
			}

			if ( ! $max_width)
			{
				// No limit, use the image width
				$max_width = $width;
			}

			if ( ! $max_height)
			{
				// No limit, use the image height
				$max_height = $height;
			}

			if ($exact)
			{
				// Check if dimensions match exactly
				return ($width === $max_width AND $height === $max_height);
			}
			else
			{
				// Check if size is within maximum dimensions
				return ($width <= $max_width AND $height <= $max_height);
			}
		}

		return FALSE;
	}

} // End upload
