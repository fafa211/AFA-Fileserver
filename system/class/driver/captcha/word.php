<?php
/**
 * Word captcha class.
 *
 * @package		Captcha
 * @subpackage	Captcha_Word
 * @author		shufa.zheng<22575353@qq.com>
 * @copyright	(c) 2015-2016 afaphp.com
 * @license		https://afaphp.com/license.html
 */
require_once 'basic.php';

class Captcha_Word extends Captcha_Basic
{
	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return string The challenge answer
	 */
	public function generate_challenge()
	{
		// Load words from the current language and randomize them
		$words = F::config('captcha.words');
		shuffle($words);

		// Loop over each word...
		foreach ($words as $word)
		{
			// ...until we find one of the desired length
			if (abs(Captcha::$config['complexity'] - strlen($word)) < 2)
				return strtoupper($word);
		}
		
		// Return any random word as final fallback
		return strtoupper($words[array_rand($words)]);
	}

} // End Captcha Word Driver Class