<?php
class PostContainBehavior extends ModelBehavior {
	public $name = 'PostContain';
	
	public $getPostContain = [];
	
	//If a value is stored in the 'postContain' within options, store it until after the find
	public function beforeFind(Model $Model, $options = []) {
		if (!empty($options['postContain'])) {
			$this->getPostContain[$Model->alias] = $options['postContain'];
			unset($options['postContain']);
		}
		return parent::beforeFind($Model, $options);
	}
	
	//If a postContain value had been stored before the find, run it
	public function afterFind(Model $Model, $results, $primary = false) {
		if (!empty($this->getPostContain[$Model->alias])) {
			$results = $this->postContain($Model, $results, $this->getPostContain[$Model->alias]);
			unset($this->getPostContain[$Model->alias]);
		}
		return $results;
	}
	
	/**
	 * Inserts values into a result mimicking the "contain" feature, but after the result has been generated
	 *
	 **/
	public function postContain(Model $Model, $result, $modelNames) {
		//debug([$Model->alias, $result, $modelNames]);
		$ids = $this->getResultIds($Model, $result);

		if (empty($ids)) {
			return false;
		}
		if (!is_array($modelNames)) {
			$modelNames = [$modelNames];
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
				$options = [];
			}

			$LinkedModel = $Model->{$modelAlias};

			if ($multiResult) {
				$limit = count($result);
			//	$limit = 40;
				for ($i = 0; $i < $limit; $i++) {
					$result[$i][$modelAlias] = [];
				}
			} else {
				$result[$modelAlias] = [];
			}

			$query = array_merge_recursive($options, [
				'fields' => [
					$LinkedModel->escapeField('*'),
				],
				'recursive' => -1,
				'conditions' => [
					$Model->escapeField() => $ids
				]
			]);
			$query['fields'][] = $Model->escapeField();

			if ($modelName == $Model->alias) {
				$modelResult = $Model->find('all', $query);
			} else {
				if (empty($query['link'])) {
					$query['link'] = [];
				} else if (!is_array($query['link'])) {
					$query['link'] = [$query['link']];
				}
				if (!array_search($Model->alias, $query['link'], true)) {
					$query['link'][] = $Model->alias;
				}
				$query['group'] = [$Model->escapeField(), $LinkedModel->escapeField()];
				
				if (empty($Model->{$modelName})) {
					throw new Exception('Could not find ' . $modelName);
				}
				$modelResult = $LinkedModel->find('all', $query);	
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
	public function getResultIds(Model $Model, $result) {
		$ids = [];
		if (isset($result[$Model->alias][$Model->primaryKey])) {
			return $result[$Model->alias][$Model->primaryKey];
		}
		if (empty($result) || !is_array($result)) {
			return [];
		}
		return Hash::extract($result, '{n}.' . $Model->alias . '.' . $Model->primaryKey);
	}
}
