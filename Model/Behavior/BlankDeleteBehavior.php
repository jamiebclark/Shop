<?php

/**
 * Watches specific columns, if they are blank on add, do not save. If they are blank on edit, remove that entry
 *
 **/
class BlankDeleteBehavior extends ModelBehavior {
	public $name = 'BlankDelete';
	
	var $settings;
	
	function setup(&$Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array(
				//All columns must match to meet criteria
				'and' => $Model->displayField,
				//Any columns must match to meet criteria
				'or' => array()
			);
		}
		if (!empty($settings)) {
			if (!is_array($settings)) {
				$settings = array('and' => $settings);
			} else if (!isset($settings['and']) && !isset($settings['or'])) {
				$settings = array('and' => $settings);
			}
			$this->settings[$Model->alias] = $settings;
		}
	}
	
	function beforeSave(&$Model, $options) {
		return $this->blankDelete($Model, $options);
	}
	
	function blankDelete(&$Model, $options = array()) {
		$isBlank = false;
		$return = true;
		if (!empty($Model->data[$Model->alias])) {
			$data =& $Model->data[$Model->alias];
		} else {
			$data =& $Model->data;
		}
		
		$settings =& $this->settings[$Model->alias];
		
		if (!empty($settings['or'])) {
			if (!is_array($settings['or'])) {
				$settings['or'] = array($settings['or']);
			}
			foreach ($settings['or'] as $column) {
				if (empty($data[$column]) || $this->_isBlank($data[$column])) {
					$isBlank = true;
				}
			}
		}
		if (!empty($settings['and'])) {
			if (!is_array($settings['and'])) {
				$settings['and'] = array($settings['and']);
			}
			$andBlank = false;
			foreach ($settings['and'] as $column) {
				if (empty($data[$column]) || $this->_isBlank($data[$column])) {
					$andBlank = true;
				} else {
					$andBlank = false;
					break;
				}
			}
			if ($andBlank) {
				$isBlank = true;
			}
		}
		if ($isBlank) {
			if (!empty($data['id'])) {
				$Model->delete($data['id']);
			}
			$Model->validationErrors = null;
			if (!empty($Model->data[$Model->alias])) {
				$Model->data[$Model->alias] = array();
			} else {
				$Model->data = array();
			}
			$return = true;
		}
		
		return $return;
	}
	
	function _isBlank($val) {
		$val = trim($val);
		$blankVals = array(
			null,
			'',
			false,
			'0000-00-00',
			'0000-00-00 00:00:00',
			'1999-11-30',
			'1999-11-30 00:00:00',
		);
		$isBlank = (array_search($val, $blankVals, true) !== false);
		return $isBlank;
	}

}
