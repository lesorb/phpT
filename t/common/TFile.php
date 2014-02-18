<?php
/**
 * TFile represents a template file being auto generated.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: TFile.php 2012-10-15 $
 * @package common.TFile
 */
class TFile
{
	const OP_NEW='new';
	const OP_OVERWRITE='overwrite';
	const OP_SKIP='skip';

	/**
	* Read and write for owner, read for everybody else
    */
	protected $chmod = 0644;
	
	/**
	* Unable to create recursive folder(s)
	*/
	protected $dirmode = 0755;
	/**
	 * @var string the file path that the new code should be saved to.
	 */
	public $path;
	/**
	 * @var mixed the newly generated code. If this is null, it means {@link path}
	 * should be treated as a directory.
	 */
	public $content;
	/**
	 * @var string the operation to be performed
	 */
	public $operation;
	/**
	 * @var string the error occurred when saving the code into a file
	 */
	public $error;

	/**
	 * Constructor.
	 * @param string $path the file path that the new code should be saved to.
	 * @param string $content the newly generated code
	 */
	public function __construct($path,$content){
		$this->path = strtr($path,array('/'=>DIRECTORY_SEPARATOR,'\\'=>DIRECTORY_SEPARATOR));
		$this->content = $content;
	
		//is file
		if(is_file($path)){
			$this->operation = file_get_contents($path) === $content ? self::OP_SKIP : self::OP_OVERWRITE;
			
		//is dir
		} else if(is_dir($path)){			
			$this->operation = $content === null ? self::OP_SKIP : self::OP_NEW;

		//is other
		} else {
			$this->operation=self::OP_NEW;
		}
	}

	/**
	 * Saves the code into the file {@link path}.
	 */
	public function save(){
		// a directory
		if($this->content===null) {
			if(!is_dir($this->path)) {

				$oldmask=@umask(0);
				$result=@mkdir($this->path,$this->dirmode,true);
				@umask($oldmask);

				if(!$result) {
					$this->error="Unable to create the directory '{$this->path}'.";
					t_write_log( $this->error,'Error' );
					return false;
				}
			}
			return true;
		}

		if($this->operation===self::OP_NEW) {
			$dir=dirname($this->path);
			if(!is_dir($dir)) {
				$oldmask=@umask(0);
				$result=@mkdir($dir,$this->dirmode,true);
				@umask($oldmask);
				if(!$result) {
					$this->error="Unable to create the directory '$dir'.";
					t_write_log( $this->error,'Error' );
					return false;
				}
			}
		}

		if(@file_put_contents($this->path,$this->content)===false) {
			$this->error="Unable to write the file '{$this->path}'.";
			t_write_log(  $this->error,'Error' );
			return false;
		} else {
			$oldmask=@umask(0);
			@chmod($this->path,$this->chmod);
			@umask($oldmask);
		}
		
		t_write_log( 'Save a file in :'. $this->path );

		return true;
	}

	/**
	 * @return string the code file extension (e.g. php, txt)
	 */
	public function getType(){
		if(($pos=strrpos($this->path,'.'))!==false)
			return substr($this->path,$pos+1);
		else
			return 'unknown';
	}
	
	/**
		another method 
		$extend = explode('.' , $fileName); 
		return end( $extend ); 
		@param string $fileName
		@return string
	*/
	public static function fileExtend( $fileName ) {		
		$extend = pathinfo($fileName,PATHINFO_EXTENSION);
		return strtolower($extend);
	}
}
