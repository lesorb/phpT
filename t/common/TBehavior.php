<?php
/**
 * TBehavior is a convenient base class for behavior classes.
 *
 * @property CComponent $owner The owner component that this behavior is attached to.
 * @property boolean $enabled Whether this behavior is enabled.
 *
 * @author Lesorb <lesorb@gmail.com>
 * @version $Id: TBehavior.php 2012-10-19 $
 * @package system.base
 */
class TBehavior extends TObject implements IBehavior{
	private $_enabled;
	private $_owner;
	
	public function events(){
		return array();
	}

	public function attach($owner){
		$this->_owner=$owner;
		foreach($this->events() as $event=>$handler)
			$owner->attachEventHandler($event,array($this,$handler));
	}

	public function detach($owner){
		foreach($this->events() as $event=>$handler)
			$owner->detachEventHandler($event,array($this,$handler));
		$this->_owner=null;
	}

	/**
	 * @return CComponent the owner component that this behavior is attached to.
	 */
	public function getOwner(){
		return $this->_owner;
	}

	/**
	 * @return boolean whether this behavior is enabled
	 */
	public function getEnabled(){
		return $this->_enabled;
	}

	/**
	 * @param boolean $value whether this behavior is enabled
	 */
	public function setEnabled($value)	{
		if($this->_enabled!=$value && $this->_owner)		{
			if($value)			{
				foreach($this->events() as $event=>$handler)
					$this->_owner->attachEventHandler($event,array($this,$handler));
			}else{
				foreach($this->events() as $event=>$handler)
					$this->_owner->detachEventHandler($event,array($this,$handler));
			}
		}
		$this->_enabled=$value;
	}
}

interface IBehavior{
	public function attach($object);
	public function detach($object);
	public function getEnabled();
	public function setEnabled($value);
}
