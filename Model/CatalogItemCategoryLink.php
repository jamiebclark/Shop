<?php
App::uses('ShopAppModel', 'Shop.Model');

class CatalogItemCategoryLink extends ShopAppModel {
	var $name = 'CatalogItemCategoryLink';
	var $belongsTo = array('Shop.CatalogItemCategory');
	
	function beforeDelete($cascade = true) {
		$result = $this->read(null, $this->id);
		//Remove corresponding Category when removing the Link
		$this->CatalogItemCategory->delete($result[$this->alias]['catalog_item_category_id']);
		return parent::beforeDelete($cascade);
	}
	
	function findByModelId($model, $modelId) {
		return $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				$this->escapeField('model') => $model,
				$this->escapeField('model_id') => $modelId,
			)
		));
	}
}