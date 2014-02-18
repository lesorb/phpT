<?php
/**
 * TControl represents a template file being auto generated.
 *
 * @author Owen Wang <owen_wang@huatek.com>
 * @version $Id: TControl.php 2012-10-15 $
 * @package common.TControl
 */
class TControl extends TMain{
	
	private $classSuff = 'Control';
	protected $_view = null;
	public $className = '';
	public $action = '';
	public $data = '';

	function __construct( $className='',$action='',$param = array() ){

		$this->_view = new TView();

		$this->className = $className;
		$this->action = $action;
		foreach ( $param as $key => $value )
			$_GET[$key] = $value;
		if($className != '')
			include( __APPPATH__ . '/c/' . $this->className.$this->classSuff . '.php' );
	}

    function control(){
        $className = $this->className.$this->classSuff;
    	$action = $this->action;
		$t = new $className();
		
		t_write_log( 'Load a controller name :'. $className );

		if( method_exists( $t,$action ) ){
			$t->$action();
			t_write_log( 'Load a controller/action :'. $className . '/' .$action );
		} else
			$this->error( 'no exist method in '.$className,'Error' );
	}
	
	function getView(){
		return $this->_view;
	}

	function render( $template, $data = array() ){
		$this->_view->view( $template,$data );
	}
}
