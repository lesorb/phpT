<?php
class aboutControl extends Tcontrol{
	
	 function index(){
	 	 
		
		$this->render('about/index.htm',array(
			'title' => '我的框架t',
			'description' => 'T框架演示2'
		));
	 }
	 
	 function other(){
	 	
		$this->render( 'about/other.htm',array(
			'title' => '我的框架t',
			'description' => 'T框架其它介绍'
		) );
	}
}
