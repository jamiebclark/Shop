<?php
class CatalogItemCategoryLink extends ShopAppModel {
	var $name = 'CatalogItemCategory';
	var $belongsTo = array('Shop.CatalogItemCategory');
	
	function beforeDelete() {
		$result = $this->read(null, $this->id);
		//Remove corresponding Category when removing the Link
		$this->CatalogItemCategory->delete($result[$this->alias]['catalog_item_category_id']);
		return parent::beforeDelete();
	}
}