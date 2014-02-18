<?php
/**
 * TBase initation of all application variable & confiuation.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: TModel.php 2012-10-15 $
 * @package common.TModel
 */
 //Tpdo::getInstance()->getConnect()->exec( 'SET NAMES utf8');
class TModel extends TMain{
	
	/*
	* table name define
	*/
	protected $tableName = '';

	private $classSuff = 'Model';
	public $db = null;
	public $className = '';
	public $data = '';
	
	function __construct( $className='',$data = array() ){
		$this->className = $className;
		$this->data = $data;
		$this->db = Tpdo::getInstance()->getConnect();
		//$this->attachBehavior( 'TVlidation','TVlidation' );
		if($className != '')
			include( __APPPATH__ . './m/' . $this->className.$this->classSuff . '.php' );
	}
	
	public function getTableName(){
		return $this->tableName;
	}
	
	function findCount( $where = array(),$condition = array() ){
		return $this->find( $where,$condition,'count' );
	}
	
	function findList( $where = array(),$condition = array() ){
		return $this->find( $where,$condition,'list' );
	}

	/**
	*
	*	Find result about object table by where conditions
	*	Generic not to be rewrited
	*
	*	@param array	where	
	*	@param array	relation
	*	@param string	type(list,item,count)
	*	@return array
	*/
	function find( $where = array(),$condition = array(),$type = 'item' ){
		
		try{

			$sql = 'SELECT ';
						
			$_select = '*';
			if(isset($condition['select']))
				$_select .= $condition['select'];
			
			if( $type === 'count' )
				$_select = 'COUNT(*)';

			$sql .= $_select . ' FROM ' . $this->getTableName();
			
			$_where = '';
			if(is_string($where))
				$_where .= $where;
			elseif(is_array($where))
				foreach( $where as $index=>$item)
					$_where .= $index . '=' . $item;
			
			if( $_where != '' )
				$sql .= ' WHERE ' . $_where;

			if(isset($condition['order_by']))
				$sql .= ' ORDER BY '.$condition['order_by'];			

			return $this->query( $sql,$type );

		} catch ( TException $e ) {
			
			t_write_log( $e->getCode() .' | ' . $e->getMessage(),'Exception' );
			throw $e;

		}
	}

	/**
	 * Excute a sql stetment
	 * if a query statement, it will cached result as a JSON
	 *
	 * @params string $sql
	 * @params string $returnType
	 * @return mixed
	 */
	function query( $sql,$returnType='item' ){
		
		t_write_log( 'Query sql is '. $sql );

		$_path = T::getPath( 'dataCache',true ) . md5( md5($sql) ).'_sql.php';
				
		$dataCache = T::$config['cache']['data'];
	
		if( is_file($_path) && $dataCache && (time()-filemtime($_path)) < T::$config['cache']['cachetime'] ){	

			$_data = include( $_path );
			$data = json_decode( $_data );
			return $data;
	
		}else{

			try{

				$sth = $this->db->prepare( $sql );
				$sth->execute();
				
				if( $returnType === 'item' )
					$data = $sth->fetch();
				elseif( $returnType === 'list' )
					$data = $sth->fetchAll();
				elseif( $returnType === 'count' )
					$data = $sth->fetchColumn();

				if(count($data)>0){
					//cache the data as josn。
					if( $dataCache ){
						$_data = "<?php return '".json_encode($data)."';";				
						$this->cached( $_path,$_data );
					}
				}

				return $data;

			} catch ( TException $e ) {
			
				t_write_log( $e->getCode() .' | ' . $e->getMessage(),'Excrption' );
				return null;
			}
		}
	}
	
	function cached( $file,$dircache ){
		$tFile = new TFile( $file,$dircache );
		t_write_log( 'Cached file is '. $file );
		return $tFile->save();
	}
	
	/*
	function __autoload($classname) {
		require_once( __APPPATH__.'./model/'.$classname . ".php" ); 
	}
	*/
}

class TVlidation extends TObject {
	
	/**
		Demo:
			$filters = array(
				"name" => array(
					"filter"=>FILTER_SANITIZE_STRING
				),
				"age" => array(
					"filter"=>FILTER_VALIDATE_INT,
					"options"=>array (
						"min_range"=>1,
						"max_range"=>120
				)),
				"email"=> FILTER_VALIDATE_EMAIL,
			 );
	*/
	function fliters( $type='post',$option=array() ){	
		if($type === 'get')
			$type = INPUT_GET;
		if($type === 'post')
			$type = INPUT_POST;

		$result = filter_input_array($type, $option);
		return $result;
	}
	
	/**
		Demo:
		$var=222;
		$options = array(
			"options"=>array(
				"min_range"=>0,
				"max_range"=>256
			)
		);
		if(!filter_var($var, FILTER_VALIDATE_INT, $options)){
			echo("Integer is not valid");
		} else {
			echo("Integer is valid");
		}
	*/
	function fliter( $var,$type=FILTER_VALIDATE_INT,$options=array() ){
		return filter_var($var, $type, $options);
	}
	
	function fliter_int( $var,$options=array() ){
		$options = am(array(
			  'flags'   => FILTER_FLAG_ALLOW_HEX,
			  'options' => array('min_range' => 1, 'max_range' => 0xff)
			),$options);
		// We must pass an associative array
		// to include the range check options.
		return $this->fliter( $var, FILTER_VALIDATE_INT,$options );
	}
}
