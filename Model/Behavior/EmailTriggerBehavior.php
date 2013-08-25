<?php
class EmailTriggerBehavior extends ModelBehavior {
	var $name = 'EmailUpdate';
	var $triggers = array();
	
	private $_triggered = array();
	
	function setup(Model $Model, $triggers = array()) {
		$this->triggers[$Model->alias] = array();
		if (!empty($triggers)) {
			$this->triggers[$Model->alias] = array_merge($this->triggers[$Model->alias], $triggers);
		}
	}
	
	function beforeSave(Model $Model, $options = array()) {
		$triggers =& $this->triggers[$Model->alias];
		if (isset($Model->data[$Model->alias])) {
			$data =& $Model->data[$Model->alias];
		} else {
			$data =& $Model->data;
		}
		foreach ($triggers as $trigger => $config) {
			if (!empty($data[$trigger])) {
				$this->_triggered[$Model->alias][$trigger] = true;
			}
		}
		return parent::beforeSave($Model, $options);
	}
	
	function afterSave(Model $Model, $created) {
		if (!empty($this->_triggered[$Model->alias])) {
			foreach ($this->_triggered[$Model->alias] as $trigger => $true) {
				$method = $this->triggers[$Model->alias][$trigger];
				if (!method_exists($Model, $method)) {
					throw new Exception("$method does not exist");
				}
				$Model->$method($Model->id);
				$this->_triggered[$Model->alias][$trigger] = false;
			}
		}
		return parent::afterSave($Model, $created);
	}
}