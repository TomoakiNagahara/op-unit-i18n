<?php
/** op-unit-i18n:/index.php
 *
 * @created   2017-07-11
 * @version   1.0
 * @package   op-unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
//	...
include('i18n.class.php');

//	...
OP\Unit::Instantiate('WebPack')->Auto(__DIR__.'/i18n.js');
