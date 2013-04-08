<?php
class DateValidateBehavior extends ModelBehavior {

	var $validated = false;
	
	function beforeValidate(&$Model) {
		$this->validated = true;
		$this->validateDates($Model);
		return true;
	}

	function beforeSave(&$Model) {
		if (!$this->validated) {
			//If user is skipping validation, make sure to call it here instead
			$this->validateDates($Model);
		}
		$this->nullDateFix($Model);
		return true;
	}

	/**
	 * Prevents blank dates saving as 0000-00-00 instead of NULL
	 *
	 **/
	private function nullDateFix(&$Model) {
		$schema = $Model->schema();
		foreach ($schema as $key => $field) {
			$null = $field['null'];
			$type = $field['type'];
			$isDate = in_array($type,array('date','datetime','timestamp'));
			if (isset($Model->data[$Model->alias][$key]) && !is_array($Model->data[$Model->alias][$key])) {
				$val = $Model->data[$Model->alias][$key];
				$blankVal = trim($val) == '' || strstr($val,'0000');
				if ($null && $isDate && $blankVal) {
					$Model->data[$Model->alias][$key] = null;
				}
			} else if ($null && $isDate) {
				$Model->data[$Model->alias][$key] = null;
			}
		}
	}

	/**
	 * Scans the Model schema to check for date, timestamp, or datetime columns and
	 * runs strtotime() on them. This allows for more flexibility in date format
	 *
	 **/
	private function validateDates(&$Model) {
		if (!empty($Model->data[$Model->alias])) {
			$data =& $Model->data[$Model->alias];
		} else {
			$data =& $Model->data;
		}
		$schema = $Model->schema();
		foreach ($schema as $key => $field) {
			if (!empty($data[$key])) {
				$val = $data[$key];
				//Allows you to pass more formats of dates
				if ($field['type'] == 'date') {
					$dateVal = $this->dateValidate($val,'Y-m-d');
					if ($dateVal) {
						$data[$key] = $dateVal;
					} else {
						$data[$key] = $field['null'] ? null : '';
					}
				} elseif (in_array($field['type'],array('timestamp','datetime'))) {
					$dateVal = $this->dateValidate($val,'Y-m-d H:i:s');
					if ($dateVal) {
						$data[$key] = $dateVal;
					}
				}
			} 
		}
		return true;
	}
	
	private function dateValidate($val, $dateFormat) {
		if (is_array($val)) {
			if (isset($val['date']) && isset($val['time'])) {
				$val = $this->dateStrValidate($val['date']).' '.$this->timeStrValidate($val['time']);
			} else {
				return false;
			}
		} else {
			$val = $this->dateTimeStrValidate($val);
		}
		if ($val != '' && ($stamp = strtotime($val))) {
			return date($dateFormat,$stamp);
		} else {
			return '';
		}
	}
	
	/**
	 * Performs last-minute changes to the date string
	 *
	 **/
	private function dateStrValidate($dateStr) {
		return $dateStr;
	}
	
	/**
	 * Performs last-minute changes to the date string
	 *
	 **/
	private function timeStrValidate($timeStr) {
		//If a user enters 1210am, it doesn't recognize it
		$timeStr = preg_replace('/12([\d]{2})[\s]*[a|A][m|M]/', '00:$1:00', $timeStr);
		return $timeStr;
	}
	
	private function dateTimeStrValidate($dateTimeStr) {
		$strs = explode(' ', $dateTimeStr);
		$return = $this->dateStrValidate(array_shift($strs));
		if (count($strs) > 0) {
			$return .= ' ' . $this->timeStrValidate(implode(' ', $strs));
		}
		return trim($return);
	}
}
