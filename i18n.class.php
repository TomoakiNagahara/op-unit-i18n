<?php
/**
 * unit-i18n:/i18n.class.php
 *
 * @created   2018-07-11
 * @updated   2019-04-08  op-app-skeleton-2019-nep
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2018-07-11
 */
namespace OP\UNIT;

/** Used class
 *
 */
use Exception;
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\IF_UNIT;
use OP\Env;
use OP\Notice;

/** i18n
 *
 * @creation  2018-07-11
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class i18n implements IF_UNIT
{
	/** trait
	 *
	 */
	use OP_CORE, OP_UNIT;

	/** Database table name.
	 *
	 * @var string
	 */
	const _table_ = 't_i18n';

	/** Length of hash key.
	 *
	 * @var integer
	 */
	const _hash_length_ = 10;

	/** Target
	 *
	 * @var string
	 */
	private $_to;

	/** Source
	 *
	 * @var string
	 */
	private $_from;

	/** Service
	 *
	 * Google, Bing, Other
	 *
	 * @var string
	 */
	private $_service;

	/** API-Key
	 *
	 * Use for service.
	 *
	 * @var string
	 */
	private $_apikey;

	/** Database object.
	 *
	 * @var \OP\UNIT\Database
	 */
	private $_DB;

	/** To hash
	 *
	 * This hash is has not been separate each App.
	 *
	 * @param	 string	 $str
	 * @return	 string	 $hash
	 */
	private function _Hash($str)
	{
		return substr(md5(join(', ', [$str, $this->_from, $this->_to])), 0, self::_hash_length_);
	}

	/** Construct
	 *
	 */
	function __construct()
	{
		//	...
		if(!$config = Env::Get('i18n') ){
			throw new Exception('Has not been set i18n config.');

		}

		//	...
		$this->_to      = $config['locale-to']   ?? null;
		$this->_from    = $config['locale-from'] ?? null;
		$this->_service = $config['service']     ?? null;
		$this->_apikey  = $config['api-key']     ?? null;
	}

	/** Get Database Object.
	 *
	 * @return \OP\UNIT\Database
	 */
	function _DB()
	{
		//	...
		if(!$this->_DB ){
			//	...
			if(!$this->_DB = $this->Unit('Database') ){
				throw new Exception('Instantiate Database object was failed.');
			};

			//	...
			if(!$this->_DB->Connect(Env::Get('i18n')['database']) ){
				throw new Exception('Connect database was failed.');
			};
		};

		//	...
		return $this->_DB;
	}

	/** Set to locale.
	 *
	 * @param	 string	 $locale
	 * @return	 string
	 */
	function To($locale)
	{
		return $this->_to = $locale;
	}

	/** Set from locale.
	 *
	 * @param	 string	 $locale
	 * @return	 string
	 */
	function From($locale)
	{
		return $this->_from = $locale;
	}

	/** Set service.
	 *
	 * @param	 string		 $service
	 * @param	 string|null $apikey
	 * @return	 string
	 */
	function Service($service, $apikey=null)
	{
		//	...
		if( $apikey ){
			$this->_apikey = $apikey;
		};

		//	...
		return $this->_service = $service;
	}

	/** Language
	 *
	 * @created  2019-04-15
	 * @param    string      $locale
	 * @return   array
	 */
	function Language($locale)
	{
		//	...
		if(!$_DB = $this->_DB() ){
			return;
		}

		//	...
		if(!$_DB->isConnect() ){
			return;
		};

		//	...
		if(!$locale ){
			Notice::Set("Locale is empty.");
			return [];
		};

		//	...
		$table = self::_table_;

		//	...
		$hash = $this->_Hash( strtolower($locale) );

		//	...
		if( $json = $_DB->Quick(" translated <- {$table}.hash = {$hash} ", ['limit'=>1]) ){
			$data = json_decode($json, true);
		}else{
			//	...
			list($lang, $country) = explode(':', $locale.':');

			//	...
			$data = $this->Unit( ucfirst($this->_service) )->Language($lang);

			//	...
			if( empty($data) ){
				Notice::Set("Data is empty.");
				return [];
			};

			//	...
			if(!$_DB = $this->_DB() ){
				return;
			}

			//	...
			if(!$_DB->isConnect() ){
				return;
			};

			//	...
			$config = [
				'table' => $table,
				'set' => [
					'hash'         => $hash,
					'from_lang'    => 'en',
					'from_country' => 'us',
					'to_lang'      => $lang,
					'to_country'   => $country,
					'original'     => '',
					'translated'   => json_encode($data),
				],
			];

			//	...
			$_DB->Insert($config);
		}

		//	...
		return $data;
	}

	/** Translate
	 *
	 * @param	 string	 $string
	 * @return	 string	 $string
	 */
	function Translate($string)
	{
		//	...
		if(!$_DB = $this->_DB() ){
			return;
		}

		//	...
		if(!$_DB->isConnect() ){
			return;
		};

		//	...
		$hash = $this->_Hash($string);

		//	...
		$table = self::_table_;

		//	...
		$translated = $_DB->Quick(" translated <- {$table}.hash = {$hash} ", ['limit'=>1]);

		//	...
		if(!$translated ){
			if(!$google = $this->Unit('Google') ){
				return;
			}

			//	...
			list($from_lang, $from_country) = explode(':', $this->_from.':');
			list($to_lang,   $to_country  ) = explode(':', $this->_to  .':');

			//	...
			if( $to_lang === $from_lang ){
				return $string;
			};

			//	...
			$translated = $google->Translate($to_lang, $from_lang, [$string], $this->_apikey)[0] ?? null;

			//	...
			if(!$translated ){
				return $string;
			};

			//	...
			$insert = [
				'table' => $table,
				'set' => [
					'hash'         => $hash,
					'from_lang'    => $from_lang,
					'from_country' => $from_country,
					'to_lang'      => $to_lang,
					'to_country'   => $to_country,
					'original'   => $string,
					'translated' => $translated,
				]
			];

			//	...
			$_DB->Insert($insert);
		}

		//	...
		return $translated;
	}

	/** Selftest
	 *
	 */
	function Selftest()
	{
		include(__DIR__.'/Selftest.class.php');
		I18N\Selftest::Auto();
	}
}
