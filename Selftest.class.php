<?php
/**
 * unit-i18n:/Selftest.class.php
 *
 * @creation  2018-03-20
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2018-03-20
 */
namespace OP\UNIT\I18N;

/** Selftest
 *
 * @creation  2018-03-20
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Selftest
{
	static function Auto()
	{
		//	Load the selftest unit.
		if(!\Unit::Load('Selftest') ){
			return;
		}

		//	If not submitted Form.
		if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
			\OP\UNIT\SELFTEST\Inspector::Form();
			return;
		}

		//	Inspection.
		\OP\UNIT\SELFTEST\Inspector::Auto(__DIR__.'/selftest/config.php');

		//	Errors.
		while( $error = \OP\UNIT\SELFTEST\Inspector::Error() ){
			echo "<p class='error'>$error</p>";
		}

		//	Get result.
		$build  = \OP\UNIT\SELFTEST\Inspector::Build();
		$failed = \OP\UNIT\SELFTEST\Inspector::Failed();
		$result = \OP\UNIT\SELFTEST\Inspector::Result();
		$config = \OP\UNIT\SELFTEST\Inspector::Config();
		D($build, $failed, $config, $result);

		//	...
		if( $failed ){
			//	Output Form.
			\OP\UNIT\SELFTEST\Inspector::Form();
		}else{
			\Html::P("Selftest was successful.",['class'=>'blue']);
		}
	}
}
