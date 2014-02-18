<?php

if( ! function_exists('am'))
{
	function am() {
		$r = array();
		$args = func_get_args();
		foreach ($args as $a) {
			if (!is_array($a)) {
				$a = array($a);
			}
			$r = array_merge($r, $a);
		}
		return $r;
	}
}

if( ! function_exists('pr'))
{
	function pr( $var ){
		echo '<pre>';
		print_r( $var );
		echo '</pre>';
	}
}

if( ! function_exists('t_write_log'))
{
	function t_write_log( $content = '',$type = 'Message' ){

		$log_bool = T::$config['log']['file'];

		if( $log_bool ){

			$dir = __APPPATH__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR;
			if( is_dir($dir) === false )
				mkdir($dir, 0775, true);

			$file_name = $dir.date("Ymd").'.log';
			if( $content !== '' ){
				$content = $type.': '.$content;
				$content = '['.date("H:i:s").']:' . $content . "\n";
				file_put_contents( $file_name, $content, FILE_APPEND );
			}
		}
	}
}

if( ! function_exists('stripslashes_deep'))
{
/**
 * Recursively strips slashes from all values in an array
 */
	function stripslashes_deep( $values ) {
		if (is_array($values)) {
			foreach ($values as $key => $value)
				$values[$key] = stripslashes_deep($value);			
		} else 
			$values = stripslashes($values);		
		return $values;
	}
}

function __autoload( $classname ) { 
  require_once ( $classname . ".php" );
}
