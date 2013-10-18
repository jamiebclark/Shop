<?php
/**
 * Automatcially updates a column (slugColumn) with the Inflector::slug value of another column (titleColumn)
 * If not set, defaults slugColumn to "slug" and titleColumn to "title"
 *
 **/
class SluggableBehavior extends ModelBehavior {
	var $settings = array();
	
	function setup(Model $Model, $settings = array()) {
		if (empty($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array();
		}
		
		$settings = array_merge(array(
			'slugColumn' => 'slug',
			'titleColumn' => $Model->displayField,
		), (array) $settings);
		
		if (!empty($settings)) {
			$this->settings[$Model->alias] = array_merge(
				$this->settings[$Model->alias],
				(array) $settings
			);
		}
	}

	function beforeSave(Model $Model, $options = array()) {
		$slugCol = $this->settings[$Model->alias]['slugColumn'];
		$titleCol = $this->settings[$Model->alias]['titleColumn'];
		if (isset($Model->data[$Model->alias][$titleCol])) {
			$Model->data[$Model->alias][$slugCol] = Inflector::slug($Model->data[$Model->alias][$titleCol]);
		}
		return true;
	}
		function findBySlug(Model $Model, $slug, $options = array()) {		$slugCol = $this->settings[$Model->alias]['slugColumn'];		$options['conditions'][][$Model->alias . '.' . $slugCol . ' LIKE'] = $slug;		return $Model->find('first', $options);	}	
	function slugRebuild(Model $Model) {
		$slugCol = $this->settings[$Model->alias]['slugColumn'];
		$titleCol = $this->settings[$Model->alias]['titleColumn'];
		$result = $Model->find('all', array(
			'fields' => array('id', $slugCol, $titleCol),
			'recursive' => -1,
		));
		$data = array();
		foreach ($result as $row) {
			$data[] = array(
				'id' => $row[$Model->alias]['id'],
				$slugCol => Inflector::slug($row[$Model->alias][$titleCol])
			);
		}
		$Model->saveAll($data);
	}
}
