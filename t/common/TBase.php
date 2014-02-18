<?php
/**
 * TBase represents a template file being auto generated.
 *
 * @author Owen Wang <owen_wang@huatek.com>
 * @version $Id: TFile.php 2012-10-15 $
 * @package common.TBase
 */
class TBase {
	
	public static $config = null;

	private static $app = null;

	private static $_aliases = array();
	
	//require( __APPPATH__ .'./config/config.php' );
	public static function setConfig( $config ){
		if( self::$config === null )
			self::$config = require( $config );
	}
	
	public static function createApp( $config ){
		self::setConfig( $config );
		self::$app = new TBase();
		return self::app();
	}
	
	public static function app(){
		return self::$app;
	}

	//$main = new TMain();
	public function run(){
		$this->_defaultSetting();
		TMain::getInstance()->error(var_export($_SERVER,true),'Message');
		TMain::getInstance()->start();
	}
	
	public static function getPath($option = 'appRoot', $anti_separator = false) {
		if ($option === 'appRoot')
			$path = __APPPATH__;
		if ($option === 'root')
			$path = __ROOTPATH__;
		if ($option === 'dataCache')
			$path = __APPPATH__ . '/cache/data/';
		if ( $option === 'configuration' )
			$path = __APPPATH__ . '/config/';

		if ($anti_separator)
			$path = str_replace('/', DIRECTORY_SEPARATOR, $path);

		return $path;
	}
	
	protected function _defaultSetting(){
		$this->_date_default_timezone_set();
		$this->_errorReportLevel();
	}

	protected function _date_default_timezone_set(){
		date_default_timezone_set( T::$config['setting']['date_timezone'] );
	}

	protected function _errorReportLevel(){
		if( T::$config['log']['level'] )
			error_reporting(E_ALL);
	}

	public function createObject( $config ){
		
		$object = null;

		if(is_string($config))
			$type = $config;
		else
			throw new TException('To be a object parameter config must be a string.');
		
		if(($n=func_num_args())>1){
			$args=func_get_args();
			if($n===2)
				$object=new $type($args[1]);
			else if($n===3)
				$object=new $type($args[1],$args[2]);
			else if($n===4)
				$object=new $type($args[1],$args[2],$args[3]);
			else
				$class = new ReflectionClass($type);
				// Note: ReflectionClass::newInstanceArgs() is available for PHP 5.1.3+
				//$object=call_user_func_array(array($class,'newInstance'),$args);
				$object = $class->newInstanceArgs($args);
		}
		
		return $object;
	}
	
	public static function getPathOfAlias($alias){
		if(isset(self::$_aliases[$alias]))
			return self::$_aliases[$alias];
		else if(($pos=strpos($alias,'.'))!==false)
		{
			$rootAlias=substr($alias,0,$pos);
			if(isset(self::$_aliases[$rootAlias]))
				return self::$_aliases[$alias]=rtrim(self::$_aliases[$rootAlias].DIRECTORY_SEPARATOR.str_replace('.',DIRECTORY_SEPARATOR,substr($alias,$pos+1)),'*'.DIRECTORY_SEPARATOR);
		}
		return false;
	}

	public static function import( $class ){

		if(class_exists($class,false) || interface_exists($class,false))
			return true;
		// a class name in PHP 5.3 namespace format
		if(($pos=strrpos($class,'\\'))!==false){
			$namespace=str_replace('\\','.',ltrim(substr($class,0,$pos),'\\'));

			if(($pos=strpos($namespace,'.'))!==false){
				$rootAlias=substr($class,0,$pos);				
				$classFile =  rtrim(str_replace('.',DIRECTORY_SEPARATOR,substr($class,$pos+1)),DIRECTORY_SEPARATOR);
				
			}else
				return false;
		}
		
		$classRootFile = self::getPath( 'root' ) . $classFile;
		$classAppFile = self::getPath() . $classFile;

		if(is_file($classRootFile))
			require($classRootFile);
		else if(is_file($classAppFile))
			require($classAppFile);
		else
			throw new TException('ClassFile is invalid. Make sure it points to an existing PHP file.');		
	}

	public function parseParams(){
		if (isset($_POST)) {
			$params['form'] = $_POST;
			if (ini_get('magic_quotes_gpc') === '1')
				$params['form'] = stripslashes_deep($params['form']);			
		}
		if (isset($_GET)) {
			if (ini_get('magic_quotes_gpc') === '1') 
				$url = stripslashes_deep($_GET);
			else
				$url = $_GET;

			if (isset($params['url']))
				$params['url'] = array_merge($params['url'], $url);
			else
				$params['url'] = $url;
		}
		return $params;
	}

}
