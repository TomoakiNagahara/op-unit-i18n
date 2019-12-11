<?php
/**
 * unit-i18n:/Selftest.class.php
 *
 * @created   2018-12-04
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2018-12-04
 */
namespace OP\UNIT\I18N;

/** Used class
 *
 */
use OP\OP_CORE;
use OP\Unit;

/** Selftest
 *
 * @creation  2018-12-04
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Selftest
{
	/** trait
	 *
	 */
	use OP_CORE;

	/** Automatically
	 *
	 */
	static function Auto()
	{
		/* @var $selftest \OP\UNIT\Selftest */
		$selftest = Unit::Instantiate('Selftest');

		//	Automatically do self test by configuration file.
		$selftest->Auto(__DIR__.'/selftest.conf.php');
	}
}
