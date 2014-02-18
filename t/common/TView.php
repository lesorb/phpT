<?php
/**
 * TView show view of template.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: TView.php 2012-10-15 $
 * @package common.TView
 */
class TView {
	
	public $data = array();

	function assign( $key,$value ) {
		if( is_array($key) ){
			foreach( $key as $index=>$item )
				$this->data[$index] = $item;
	   	}else
			$this->data[$key] = $value;	   
	}

	function view( $element,$data ) {

		foreach( $data as $_index=>$_data )
			$this->assign( $_index,$_data );
		
		$context = new TViewRender();		
		$context->renderFile( $element,$this->data );
	}	
}

/**
 * TViewRender render a view element of template for .
 *
 * @author Owen Wang <owen_wang@huatek.com>
 * @version $Id: TView.php 2012-10-16 $
 * @package common.TView
 */
class TViewRender {
	
	//private $filePermission = 0755;
	
	protected $suffix = '.htm';

	public function __construct(){
		if( T::$config['view']['suffix'] != ''  )
			$this->suffix = T::$config['view']['suffix'];
		header( 'Content-Type:text/html;charset=UTF-8' );
	}

	public function renderFile( $element,$data,$return=false )
	{
		$sourceFile = __APPPATH__ . '/v/' . $element;		

		$_suffix = TFile::fileExtend( $sourceFile );
		if( $_suffix === '' )
			$sourceFile .= $this->suffix;

		if(!is_file($sourceFile) || ($file=realpath($sourceFile))===false)
			throw new TException( 'this file is not exist in '. $sourceFile );
			
		t_write_log( 'Render a view file :'. $sourceFile );

		return $this->renderInternal( $sourceFile,$data,$return );
	}

	public function renderPartial( $element,$data=array() )
	{
		return $this->renderFile( $element,$data,true );
	}
	
	private function renderInternal($_viewFile_,$_data_=null,$_return_=false)
	{
		// we use special variable names here to avoid conflict when extracting data
		if(is_array($_data_))
			extract($_data_,EXTR_PREFIX_SAME,'data');
		else
			$data=$_data_;
		if($_return_)
		{
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			echo ob_get_clean();
		}
		else
			require($_viewFile_);
	}
		
}
