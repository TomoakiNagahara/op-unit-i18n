<?php
/**
 * module-testcase:/unit/i18n/action.php
 *
 * @creation  2019-04-08
 * @version   1.0
 * @package   module-testcase
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2019-04-08
 */
namespace OP;

/* @var $app  UNIT\App  */
$app->Template('menu.phtml');

/* @var $i18n UNIT\i18n */
$i18n = $app->Unit('i18n');

//	...
$args = $app->Args();

//	...
$locale = Cookie::Get('locale') ?? 'ja:JP';

//	...
$i18n->To($locale);

//	...
$source    = 'This is i18n translation test.';

//	...
switch( $args[2] ?? null ){
	case 'selftest':
		$i18n->Selftest();
		break;

	case 'languages':
		D( $i18n->Language($locale) );
		break;

	case 'translate':
		//	...
		$translate = $i18n->Translate($source);

		//	...
		D($locale, $source, $translate);
		break;

	case 'javascript':
		$app->Template('javascript.phtml');
		break;
};
