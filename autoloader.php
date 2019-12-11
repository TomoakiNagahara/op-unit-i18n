<?php
/**
 * unit-i18n:/autoloader.php
 *
 * @created   2018-02-16
 * @version   1.0
 * @package   unit-i18n
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
//	...
spl_autoload_register( function($name){
	//	...
	$UNIT = 'I18N';
	$unit = strtolower($UNIT);
	$Unit = ucfirst($unit);

	//	...
	$name = trim($name, '\\');

	//	...
	$namespace = "OP\UNIT\\{$UNIT}";

	//	...
	if( $name === "OP\UNIT\\{$unit}" ){
		$name  =  $unit;
	}else
		if( strpos($name, $namespace) === 0 ){
		$name = substr($name, strlen($namespace)+1);
	}else{
		return;
	}

	//	...
	$path = __DIR__."/{$name}.class.php";

	//	...
	if( file_exists($path) ){
		include($path);
	}else{
		Notice::Set("Does not exists this file. ($path)");
	}
});
