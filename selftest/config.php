<?php
/**
 * unit-selftest:/selftest/config.php
*
* @created   2018-03-19
* @version   1.0
* @package   unit-selftest
* @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
* @copyright Tomoaki Nagahara All right reserved.
*/
//	...
$config = [];

//	...
$user = [];
$user['driver']	 = 'mysql';
$user['host']	 = 'localhost';
$user['port']	 = '3306';
$user['user']	 = 'testcase';
$user['password']= 'testcase';
$user['charset'] = 'utf8';

//	...
$config['users'][] = $user;

//	...
$structure = [];

//	...
$database_name = 'testcase';
$database = [];
$database['name'] = $database_name;

//	...
$table_name = 't_test';
$table = [];
$table['name']	 = $table_name;
$table['ai']	 = 'ai';

//	...
$column = [];
$column_name = 'ai';
$column['name']	 = $column_name;
$column['type']	 = 'int';
$table['columns'][$column_name] = $column;

//	...
$column = [];
$column_name = 'id';
$column['name']		 = $column_name;
$column['type']		 = 'char';
$column['length']	 =  8;
//$column['unique']	 =  true;
$column['key']		 = 'unique';
$table['columns'][$column_name] = $column;

//	...
$column = [];
$column_name = 'tag';
$column['name']		 = $column_name;
$column['type']		 = 'varchar';
$column['length']	 =  10;
$table['columns'][$column_name] = $column;

//	...
$column = [];
$column_name = 'number';
$column['name']		 = $column_name;
$column['type']		 = 'int';
$table['columns'][$column_name] = $column;

//	...
$database['tables'][$table_name] = $table;

//	...
$structure['databases'][$database_name] = $database;

//	...
$config['structures'][] = $structure;

//	...
return $config;
