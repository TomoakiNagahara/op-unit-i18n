<?php
/**
 * unit-i18n:/i18n.class.php
 *
 * @creation  2018-02-16
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2018-02-16
 */
namespace OP\UNIT;

/** i18n
 *
 * @creation  2018-02-16
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class i18n
{
	/** trait
	 *
	 */
	use \OP_CORE;

	/** Database configuration.
	 *
	 * @return array
	 */
	function _Config()
	{
		return \Env::Get('i18n', [
			'driver'	 => 'mysql',
			'host'		 => 'localhost',
			'port'		 => '3306',
			'user'		 => 'i18n',
			'password'	 => 'i18n',
			'database'	 => 'onepiece',
			'charset'	 => 'utf8',
		]);
	}

	/** Get default country.
	 *
	 * @param unknown $lang
	 */
	private function _Country($lang)
	{
		//	...
		switch( $lang ){
			case 'en':
				$country = 'us';
				break;
			default:
			$country = '';
		}

		//	...
		return $country;
	}

	/** Get/Set Locale and parse.
	 *
	 * @param	string	$which
	 * @param	string	$locale
	 * @return	array	[$lang, $country]
	 */
	private function _Locale($which, $locale=null)
	{
		//	...
		static $to, $from;

		//	...
		switch( $which ){
			case 'from':
				if( $locale ){
					$from = $locale;
				}else{
					$locale = $from;
				}
				break;
			case 'to':
				if( $locale ){
					$to = $locale;
				}else{
					$locale = $to;
				}
				break;
			default:
		}

		//	...
		if( $pos = strpos($locale, '-') ){
			$lang    = substr($locale, 0, $pos);
			$country = substr($locale, $pos +1);
		}else{
			$lang    = $locale;
			$country = $this->_Country($lang);
		}

		//	...
		return [$lang, $country];
	}

	/** DB
	 *
	 * @return	\OP\UNIT\DB $_DB
	 */
	private function _DB()
	{
		/* @var $_DB \OP\UNIT\DB */
		static $_DB;

		//	...
		if( $_DB === false ){
			throw new \Throwable("DB was not installed?");
		}

		//	...
		if( $_DB === null ){
			$_DB = \Unit::Factory('DB');
			$_DB->Connect( self::_Config() );
		}

		//	...
		return $_DB;
	}

	/** SQL
	 *
	 * @return	\OP\UNIT\SQL $_SQL
	 */
	private function _SQL()
	{
		/* @var $_SQL \OP\UNIT\SQL */
		static $_SQL;

		//	...
		if( $_SQL === false ){
			throw new \Throwable("DB was not installed?");
		}

		//	...
		if( $_SQL === null ){
			$_SQL = \Unit::Factory('SQL');
		}

		//	...
		return $_SQL;
	}

	/** Unit of Google.
	 *
	 * @return	\OP\UNIT\Google
	 */
	function Google()
	{
		/* @var $unit \OP\UNIT\Google */
		static $unit;

		//	...
		if( $unit === false ){
			throw new \Throwable("Google unit was not installed?");
		}

		//	...
		if( $unit === null ){
			$unit = \Unit::Factory('Google');
		}

		//	...
		return $unit;
	}

	/** Generate Hash.
	 *
	 * @param	string	$source
	 * @param	string	$lang
	 * @param	string	$country
	 * @return	string	$hash
	 */
	function Hash($string, $lang_to, $country_to, $lang_from, $country_from)
	{
		return Hasha1("$string, $lang_to, $country_to, $lang_from, $country_from");
	}

	/** Translate
	 *
	 * @param	string	$string
	 * @param	string	$locale_to
	 * @param	string	$locale_from
	 * @return	string	$translated
	 */
	function Translate($string, $locale_to=null, $locale_from=null)
	{
		//	...
		list($lang_to  , $country_to)   = $this->_Locale('to'  , $locale_to  );
		list($lang_from, $country_from) = $this->_Locale('from', $locale_from);

		//	...
		$hash = $this->Hash($string, $lang_to, $country_to, $lang_from, $country_from);

		//	...
		$translated = $this->_DB()->QQL(" translated <- i18n.hash = $hash ", ['limit'=>1]);

		//	...
		if(!$translated ){
			//	...
			$translated = $this->Google()->Translation($lang_from, $lang_to, $string);

			//	...
			if( $translated ){
				//	...
				$insert['table'] = 'i18n';
				$insert['set']['hash']		 = $hash;
				$insert['set']['to_lang']	 = $lang_to;
				$insert['set']['to_country'] = $country_to;
				$insert['set']['from_lang']	 = $lang_from;
				$insert['set']['from_country'] = $country_from;
				$insert['set']['origin']	 = $string;
				$insert['set']['translated'] = $translated;

				//	...
				$query = $this->_SQL()->Insert($insert, $this->_DB());

				//	...
				$this->_DB()->Query($query,'insert');
			}
		}

		//	...
		return $translated;
	}

	/** Selftest
	 *
	 */
	function Selftest()
	{
		Selftest::Auto();
	}
}
