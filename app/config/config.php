<?php
return array(
	'site' => 'My First T Application',
	'base_url' => '/t2/',
	'setting' => array(
		'language' => 'en',
		'date_timezone' => 'Asia/Shanghai',
	),
	'db' => array(
		//'type' => 'mysql',
		'connection' => 'mysql:host=localhost;dbname=t',
		'user' => 'root',
		'pass' => '111111',
		'charset' => 'utf8',
	),
	'view' => array(
		'suffix' => ''
	),
	'cache' => array(
		'cachetime' => 3600,//24 * 3600,
		'page' => false,
		'data' => false,
		'template' => false,
		'view' => false,
	),
	'log' => array(
		'level' => 1,
		'file' => true,
	),

);
