<?php
class PostContainBehavior extends ModelBehavior {
	var $name = 'PostContain';
	
	var $getPostContain = array();
	
	//If a value is stored in the 'postContain' within options, store it until after the find
	function beforeFind(Model $Model, $options = array()) {
		if (!empty($options['postContain'])) {
			$this->getPostContain[$Model->alias] = $options['postContain'];
			unset($options['postContain']);
		}

		return parent::beforeFind($Model, $options);
	}
	
	//If a postContain value had been stored before the find, run it
	function afterFind(Model $Model, $results, $primary = false) {
		if (!empty($this->getPostContain[$Model->alias])) {
			$results = $this->postContain($Model, $results, $this->getPostContain[$Model->alias]);
			unset($this->getPostContain[$Model->alias]);
		}
		return parent::afterFInd($Model, $results, $primary);
	}
	
	/**
	 * Inserts values into a result mimicking the "contain" feature, but after the result has been generated
	 *
	 **/
	function postContain(Model $Model, $result, $modelNames) {
		//debug(array($Model->alias, $result, $modelNames));
		$ids = $this->ids($Model, $result);
		if (empty($ids)) {
			return false;
		}
		if (!is_array($modelNames)) {
			$modelNames = array($modelNames);
		}
		
		//True if result of "find all", false if "find first"
		$multiResult = !isset($result[$Model->alias]['id']);
		
		//$Model = new $Model->alias();
		foreach ($modelNames as $key => $modelAlias) {
			if (is_array($modelAlias)) {
				$options = $modelAlias;
				$modelAlias = $key;
				$modelName = Param::keyCheck($options, 'modelName', true, $modelAlias);
				//list($modelName, $options) = $modelName;
			} else {
				$modelName = $modelAlias;
				$options = array();
			}
			
			if ($multiResult) {
				$limit = count($result);
			//	$limit = 40;
				for ($i = 0; $i < $limit; $i++) {
					$result[$i][$modelAlias] = array();
				}
			} else {
				$result[$modelAlias] = array();
			}

			$options = array_merge_recursive($options, array(
				'fields' => array('*'),
				'recursive' => -1,
				'conditions' => array(
					$Model->alias. '.' . $Model->primaryKey => $ids
				)
			));
			//ddebug($options);
			if ($modelName == $Model->alias) {
				$modelResult = $Model->find('all', $options);
			} else {
				if (empty($options['link'])) {
					$options['link'] = array();
				} else if (!is_array($options['link'])) {
					$options['link'] = array($options['link']);
				}
				if (!array_search($Model->alias, $options['link'], true)) {
					$options['link'][] = $Model->alias;
				}
				$modelResult = $Model->{$modelName}->find('all', $options);
			}
			
			if (!empty($modelResult)) {
				foreach ($modelResult as $modelRow) {
					$linkId = $modelRow[$Model->alias][$Model->primaryKey];
					$insert = $modelRow[$modelName];
					unset($modelRow[$modelName]);
					if (!empty($modelRow)) {
						$insert = array_merge($insert, $modelRow);
					}
					
					unset($insert[$Model->alias]);
					
					if ($multiResult) {
						$key = array_search($linkId, $ids, true);
						if (!empty($result[$key])) {
							$result[$key][$modelAlias][] = $insert;
						}
					} else {
						$result[$modelAlias][] = $insert;
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * Returns the primaryKeys of a found result
	 *
	 **/
	function ids(Model $Model, $result) {
		$ids = array();
		if (isset($result[$Model->alias][$Model->primaryKey])) {
			return $result[$Model->alias][$Model->primaryKey];
		}
		if (empty($result) || !is_array($result)) {
			return array();
		}
		
		foreach ($result as $row) {
			if (!empty($row[$Model->alias][$Model->primaryKey])) {
				$ids[] = $row[$Model->alias][$Model->primaryKey];
			}
		}
		return $ids;
	}
}
