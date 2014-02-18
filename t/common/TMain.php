<?php
/**
 * TMain define main application run engine.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: TMain.php 2012-10-15 $
 * @package common.TMain
 */
class TMain extends TObject{
	
	protected $_path = '';
	
	static private $_instance = null;

	/**
	* construct main
	*/
	private function __construct(){
	}
	
	public static function getInstance() {
		if( self::$_instance === null )
			self::$_instance = new TMain();
		return self::$_instance;
	}

	public function update(TObject $object){}

	/**
	* Start a application of instance
	*/
	public function start() {
		
		t_write_log( 'Start a application '. T::$config['site'] );

		$url = $_SERVER['REQUEST_URI'];

		$baseUrl = T::$config['base_url'];
		if( !empty( $baseUrl ) ){
			$_url = explode( $baseUrl,$url );
			$url = $_url[1];
		}

		$arr = $url ? explode('?',$url) : array();
		
		t_write_log( 'url request: '. $url );

		if( isset($arr[0]) )
			$this->_path = $arr[0];			
		else
			$this->_path = 'index/index';
		
		$this->getControl();
	}
	
	/**
	* Get controller of app
	*/
	function getControl( $path = '' ){
		
		$data = array();

		$path = $this->_handle_slash( $path );
	
		$array = explode( '/',$path );

		for( $i=1;$i<count($array)/2;$i++ )
			$data[$array[$i*2]] = $array[$i*2+1];

		$className = $array[0];
		$action = empty($array[1]) ? 'index' : $array[1]; 
		$appC = new TControl( $className,$action,$data );		
		$appC->control();
	}
		
	/**
	*	fetch the model object
	*/
	function getModel( $modelName,$data=array() ){		
		if(!empty($modelName)){
			new TModel( $modelName,$data );	
			t_write_log( 'Load a Model '. $modelName );
		}else
			$this->error( 'getModel first parameter is not empty!' );					
	}

	/**
	*	throw error
	*/
	function error( $error,$type='Warning' ){
		//echo $type.': '.$error;
		//throw new TException( 'Exception message'.$error );
		if( T::$config['log']['level'] ){
			$traces=debug_backtrace();
			$count=0;
			foreach($traces as $trace){
				if(isset($trace['file'],$trace['line']))
					$error .= "\nin ".$trace['file'].' ('.$trace['line'].')';			
			}
		}
		t_write_log( $error,$type );
	}
	
	private function _handle_slash( $path = '' ){
		if( $path == '' )
			$path = $this->_path;
		$path = chop(chop($path),'/');

		$this->error($path,'Message');

		return $path;
	}
	/*
	function __autoload( $className ) {
		require_once( __APPPATH__.'./'. get_class($this) .'/'.$className . ".php" ); 
	}*/
}
