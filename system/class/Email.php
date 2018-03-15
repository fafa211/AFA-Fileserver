<?php
/**
 * Email helper class.
 *
 * $Id: email.php 4134 2009-03-28 04:37:54Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Email {
	
	// SwiftMailer instance
	protected static $mail;
	
	// email config options
	protected static $options;
	
	/**
	 * Creates a SwiftMailer instance.
	 *
	 * @param   string  DSN connection string
	 * @return  object  Swift object
	 */
	public static function connect($config = NULL) {
		
		if (! class_exists ( 'Swift', FALSE )) {
			// Load SwiftMailer
			require('swift/Swift.php');
			
			// Register the Swift ClassLoader as an autoload
			spl_autoload_register ( array ('Swift_ClassLoader', 'load' ) );
		}
		
		// Load default configuration
		($config === NULL) and $config = F::config('email');
		if (self::$options == NULL && isset ( $config ['options'] ))
			self::$options = $config ['options'];
		
		switch ($config ['driver']) {
			case 'smtp' :
				// Set port
				$port = empty ( $config ['options'] ['port'] ) ? NULL : ( int ) $config ['options'] ['port'];
				
				if (empty ( $config ['options'] ['encryption'] )) {
					// No encryption
					
					Swift_ClassLoader::load('Swift_Connection_SMTP');
					$encryption = Swift_Connection_SMTP::ENC_OFF;
				} else {
					// Set encryption
					switch (strtolower ( $config ['options'] ['encryption'] )) {
						case 'tls' :
							$encryption = Swift_Connection_SMTP::ENC_TLS;
							break;
						case 'ssl' :
							$encryption = Swift_Connection_SMTP::ENC_SSL;
							break;
					}
				}
				
				// Create a SMTP connection
				$connection = new Swift_Connection_SMTP ( $config ['options'] ['hostname'], $port, $encryption );
				
				// Do authentication, if part of the DSN
				empty ( $config ['options'] ['username'] ) or $connection->setUsername ( $config ['options'] ['username'] );
				empty ( $config ['options'] ['password'] ) or $connection->setPassword ( $config ['options'] ['password'] );
				
				if (! empty ( $config ['options'] ['auth'] )) {
					// Get the class name and params
					list ( $class, $params ) = arr::callback_string ( $config ['options'] ['auth'] );
					
					if ($class === 'PopB4Smtp') {
						// Load the PopB4Smtp class manually, due to its odd filename
						require ('swift/Swift/Authenticator/$PopB4Smtp$');
					}
					
					// Prepare the class name for auto-loading
					$class = 'Swift_Authenticator_' . $class;
					
					// Attach the authenticator
					$connection->attachAuthenticator ( ($params === NULL) ? new $class () : new $class ( $params [0] ) );
				}
				
				// Set the timeout to 5 seconds
				$connection->setTimeout ( empty ( $config ['options'] ['timeout'] ) ? 5 : ( int ) $config ['options'] ['timeout'] );
				break;
			case 'sendmail' :
				// Create a sendmail connection
				Swift_ClassLoader::load('Swift_Connection_Sendmail');
				$connection = new Swift_Connection_Sendmail ( empty ( $config ['options'] ) ? Swift_Connection_Sendmail::AUTO_DETECT : $config ['options'] );
				
				// Set the timeout to 5 seconds
				$connection->setTimeout ( 5 );
				break;
			default :
				// Use the native connection
				Swift_ClassLoader::load('Swift_Connection_NativeMail');
				$connection = new Swift_Connection_NativeMail ( $config ['options'] );
				break;
		}
		
		// Create the SwiftMailer instance
		return Email::$mail = new Swift ( $connection );
	}
	
	/**
	 * Send an email message.
	 *
	 * @param   string|array  recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string|array  sender email (and name)
	 * @param   string        message subject
	 * @param   string        message body
	 * @param   boolean       send email as HTML
	 * @return  integer       number of emails sent
	 */
	public static function send($to, $from, $subject, $message, $html = FALSE, $swiftfile = null) {
		// Connect to SwiftMailer
		(Email::$mail === NULL) and Email::connect ();
		
		// Determine the message type
		$html = ($html === TRUE) ? 'text/html' : 'text/plain';
		
		// Create the message
		$message = new Swift_Message ( $subject, $message, $html, '8bit', 'utf-8' );
		
		if (is_string ( $to )) {
			// Single recipient
			$recipients = new Swift_Address ( $to );
		} elseif (is_array ( $to )) {
			if (isset ( $to [0] ) and isset ( $to [1] )) {
				// Create To: address set
				$to = array ('to' => $to );
			}
			
			// Create a list of recipients
			$recipients = new Swift_RecipientList ();
			
			foreach ( $to as $method => $set ) {
				if (! in_array ( $method, array ('to', 'cc', 'bcc' ) )) {
					// Use To: by default
					$method = 'to';
				}
				
				// Create method name
				$method = 'add' . ucfirst ( $method );
				
				if (is_array ( $set )) {
					// Add a recipient with name
					$recipients->$method ( $set [0], $set [1] );
				} else {
					// Add a recipient without name
					$recipients->$method ( $set );
				}
			}
		}
		
		if (self::$options != NULL && isset ( self::$options ['from'] ))
			$from = self::$options ['from'];
		if (is_string ( $from )) {
			// From without a name
			$from = new Swift_Address ( $from );
		} elseif (is_array ( $from )) {
			// From with a name
			$from = new Swift_Address ( $from [0], $from [1] );
		}
		if ($swiftfile) {
			$swiftfileobj = new Swift_File ( $swiftfile );
			$attachment = new Swift_Message_Attachment ( $swiftfileobj );
			$message->attach ( $attachment );
		}
		
		return Email::$mail->send ( $message, $recipients, $from );
	}
	
	/**
	 * Add to email queue.
	 *
	 * @param   string|array  recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string        message subject
	 * @param   string        message body
	 * @param   boolean       send email as HTML
	 * @return  boolean
	 */
	public static function addToQueue($to, $subject, $message, $html = FALSE, $priority = 0) {
		$db = Database::instance ();
		return ( bool ) count ( $db->insert ( 'email_queue', array ('subject' => $subject, 'message' => $message, 'type' => $html ? 'html' : 'text', 'to' => is_array ( $to ) ? join ( ';', $to ) : $to, 'create_time' => date ( 'Y-m-d H:i:s' ), 'priority' => $priority ) ) );
	}
	
	/**
	 * view the using options
	 *
	 */
	public static function getOptions() {
		return self::$options;
	}
} // End email