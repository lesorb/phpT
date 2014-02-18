<?PHP
/**
 * Tpdo initation intanse of PDO extend of PHP.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: Tpdo.php 2012-10-15 $
 * @package common.Tpdo
 */
class Tpdo 
{
	
	static private $_instance = null;
	
	private $_connect = array();
	
	public function getConnect()
	{
		return $this->_connect;
	}

	public static function getInstance()
	{
		if( self::$_instance === null ){
			self::$_instance = new Tpdo();
			self::$_instance->initPDO();
			self::$_instance->getConnect()->exec( 'SET NAMES '.T::$config['db']['charset'] );
		}
		return self::$_instance;
	}

	private function initPDO()
	{
		try{
			$config = T::$config['db'];
			$this->_connect = new PDO( $config['connection'], $config['user'], $config['pass'] );
			$this->_connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch( PDOException $ex ) {
			// Note The Typecast To An Integer!
			t_write_log( 'PDO ' . (int)$ex->getCode() .' | ' . $ex->getMessage(),'Error' );
			throw new TDatabaseException( $ex->getMessage( ) , (int)$ex->getCode( ) );
		}
	}

}