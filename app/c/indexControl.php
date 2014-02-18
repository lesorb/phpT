<?php

class indexControl extends TControl{

	function index(){
	
		$this->render( 'index/index' ,array(
			'title' => 'T show',
			'description' => 'T show 第一个 T 框架演示 '
		) );
	 }

	function test(){
		
		$this->getModel( 'test' );
		$test = new testModel();

		if( isset($_GET['id']) )
			$data = $test->find( array( '`id`'=> $_GET['id'] ) );
		else
			$data = array();

		$dataList = $test->findList( 'nom_transporteur <> \'\'' );
		$dataCount = $test->findCount( 'nom_transporteur <> \'\'' );
		
		$this->render( 'index/test' , array(
			'title' => 'T show test',
			'description' => 'T is a tiny framework of PHP language, this is version No 1 now',
			'test' => $data,
			'dataCount' => $dataCount,
			'dataList' => $dataList,
		) );
	}

}
