<?php

abstract class TObject{
	
	private $_e = null;
	private $_m = null;

	public function attachBehaviors($behaviors)	{
		foreach($behaviors as $name=>$behavior)
			$this->attachBehavior($name,$behavior);
	}
	public function detachBehaviors()	{
		if($this->_m!==null)		{
			foreach($this->_m as $name=>$behavior)
				$this->detachBehavior($name);
			$this->_m=null;
		}
	}
	
	public function attachBehavior($name,$behavior)	{
		if(!($behavior instanceof IBehavior))
			$behavior=T::createObject($behavior);
		$behavior->setEnabled(true);
		$behavior->attach($this);
		return $this->_m[$name]=$behavior;
	}

	public function detachBehavior($name)	{
		if(isset($this->_m[$name]))		{
			$this->_m[$name]->detach($this);
			$behavior=$this->_m[$name];
			unset($this->_m[$name]);
			return $behavior;
		}
	}
	
	public function enableBehaviors(){
		if($this->_m!==null){
			foreach($this->_m as $behavior)
				$behavior->setEnabled(true);
		}
	}
	public function disableBehaviors(){
		if($this->_m!==null){
			foreach($this->_m as $behavior)
				$behavior->setEnabled(false);
		}
	}
	public function enableBehavior($name){
		if(isset($this->_m[$name]))
			$this->_m[$name]->setEnabled(true);
	}
	public function disableBehavior($name){
		if(isset($this->_m[$name]))
			$this->_m[$name]->setEnabled(false);
	}
	
	public function hasEvent($name) {
		return !strncasecmp($name,'on',2) && method_exists($this,$name);
	}
	
	public function getEventHandlers($name)	{
		if($this->hasEvent($name))	{
			$name=strtolower($name);
			if(!isset($this->_e[$name]))
				$this->_e[$name]=new TEvent();
			return $this->_e[$name];
		}else
			throw new TException('Event "{class}.{event}" is not defined.');
	}
	
	public function attachEventHandler($name,$handler){
		$this->getEventHandlers($name)->add($handler);
	}

	public function detachEventHandler($name,$handler){
		if($this->hasEventHandler($name))
			return $this->getEventHandlers($name)->remove($handler)!==false;
		else
			return false;
	}

	public function __call($name,$parameters){
		if($this->_m!==null){
			foreach($this->_m as $object){
				if($object->getEnabled() && method_exists($object,$name))
					return call_user_func_array(array($object,$name),$parameters);
			}
		}
		throw new TException('{class} and its behaviors do not have a method or closure named "{name}".');
	}
}

class TEvent extends TObject
{
	public $sender;
	public $handled=false;
	public $params;
	public function __construct($sender=null,$params=null)
	{
		$this->sender=$sender;
		$this->params=$params;
	}
}
