<?php
//Stores an array of values before
class ChangedFieldsBehavior extends ModelBehavior {

	function setup(&$Model, $settings = array()) {
		$Model->changedFields = array();
	}
	
	function beforeSave(&$Model) {
		$Model->changedFields = $this->getChangedFields($Model);
		return true;
	}
	
	function getChangedFields(&$Model) {
		$changedFields = array();
		if (!empty($Model->data[$Model->alias])) {
			$data = $Model->data[$Model->alias];
		} else {
			$data = $Model->data;
		}
		$Model->recursive = -1;
		
		$saveData = $Model->data;
		$Model->old = $Model->read(null,$Model->id);
		//Resets data after read
		$Model->data = $saveData;
		
		if ($Model->old){
			foreach ($data as $key =>$value) {
				if (isset($Model->old[$Model->alias][$key]) && $Model->old[$Model->alias][$key] != $value) {
					$changedFields[] = $key;
				}
			}
		}
		// $changedFields is an array of fields that changed
		return $changedFields;
	}
}
