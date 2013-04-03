<?php
/**
 * Field Order Behavior for CakePHP 1.3
 * 
 * Enables a model to sort its fields based on a field in the table.
 * Optionally that order field can be sub-divided by other fields within the table.
 *
 **/
 
class FieldOrderBehavior extends ModelBehavior {
	var $subKeyFields;
	var $orderField;
	
	var $_cacheId;
	var $_cacheConditions;
	
	var $settings = array();
	
	function setup(&$Model, $settings=array()) {
		if(!is_array($settings) && !empty($settings)) {
			$settings = array(
				'subKeyFields' => array($settings)
			);
		}
		//Default settings
		$settings = array_merge(array(
			'orderField' => 'sub_order',
			'subKeyFields' => null
			), $settings
		);
		
		if (!empty($settings['subKeyFields']) && !is_array($settings['subKeyFields'])) {
			$settings['subKeyFields'] = array($settings['subKeyFields']);
		}
		if (empty($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array();
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
		
		
		//$this->orderField = $config['orderField'];
		//$this->subKeyFields = $config['subKeyFields'];

		//$this->_set($config);
		$this->setModelOrder($Model);
	}
	
	function setModelOrder(&$Model) {
		$settings =& $this->settings[$Model->alias];
		if (count($settings['subKeyFields']) > 0) {
			$order = array();
			foreach ($settings['subKeyFields'] as $field) {
				$order[] = $Model->alias . '.' . $field;
			}
			$Model->order = $order;
		} else {
			$Model->order = array();
		}
		$Model->order[] = $Model->alias . '.' . $settings['orderField'];
	}
	
	/*
	function afterDelete(&$Model) {
		if (is_numeric($Model->id)) {
			$this->reorder($Model,$Model->id);
		}
	}
	*/
	
	function afterSave(&$Model, $created) {
		if ($created) {
			$this->moveLast($Model, $Model->id);
		}
		return true;
	}
	
	function beforeDelete(&$Model) {
		$this->moveLast($Model, $Model->id);
		return true;
	}
		
	function moveUp(&$Model, $id, $delta = 1) {
		return $this->adjustOrder($Model, $id, $delta * -1);
	}
	function moveDown(&$Model, $id, $delta = 1) {
		return $this->adjustOrder($Model, $id, $delta);
	}
	
	function moveFirst(&$Model, $id) {
		return $this->setOrder($Model, $id, 0);
	}
	
	function moveLast(&$Model, $id) {
		return $this->setOrder($Model, $id, 99999999);
	}
	
	function adjustOrder(&$Model, $id, $delta = 1) {
		$settings =& $this->settings[$Model->alias];
		//Loads info on id
		$Model->create();
		$result = $Model->read(null, $id);
		if (empty($result)) {
			return false;
		}
		$order = $result[$Model->alias][$settings['orderField']];
		
		$newOrder = $order + $delta;
		
		return $this->setOrder($Model, $id, $newOrder);
	}
	
	function setOrder(&$Model, $id = null, $newOrder = null) {
		$settings =& $this->settings[$Model->alias];
		$result = $this->__getPeers($Model, $id);
		return $this->_reorderResult($Model, $result, $id, $newOrder);
	}
	
	//Reorders a table on a specific field based on a set of conditions and order commands
	function updateOrderField(&$Model, $orderField, $conditions = array(), $order = array()) {
		if (empty($order)) {
			$order = array($orderField);
		}
		$result = $Model->find('all', compact('conditions', 'order') + array('recursive' => -1));
		return $this->_reorderResult($Model, $result, null, null, $orderField);
	}
	
	function _reorderResult($Model, $result, $id = null, $newOrder = null, $orderField = null) {
		$data = array();
		$settings =& $this->settings[$Model->alias];
		if (empty($orderField)) {
			$orderField = $settings['orderField'];
		}
		
		if (!empty($id)) {
			$total = count($result);
			if ($newOrder < 1) {
				$newOrder = 1;
			} else if ($newOrder > $total) {
				$newOrder = $total;
			}
		}
		
		$count = 0;
		foreach ($result as $row) {
			$rowId = $row[$Model->alias][$Model->primaryKey];
			if ($id == $rowId) {
				$setCount = $newOrder;
			} else {
				$setCount = ++$count;
				if ($count == $newOrder) {
					$setCount = ++$count;
				}
			}
			$data[] = array(
				$Model->primaryKey => $rowId,
				$orderField => $setCount,
			);
		}
		return $Model->saveAll($data, array('callbacks' => false, 'validate' => false));
	}
	
	
	function __getPeers(&$Model, $id) {
		$settings =& $this->settings[$Model->alias];
		$conditions = array();
		$order = array();
		if(is_array($settings['subKeyFields'])) {
			$result = $Model->read(null, $id);
			foreach($settings['subKeyFields'] as $field) {
				if (!empty($result[$Model->alias][$field])) {
					$conditions[$Model->alias . '.' . $field] = $result[$Model->alias][$field];
					$order[] = $Model->alias . '.' . $field;
				}
			}
		}
		$order[] = $Model->alias.'.'.$settings['orderField'];
		return $Model->find('all', compact('conditions', 'order'));
	}
	
}
