<?php
class CatalogItemCategoryLinkBehavior extends ModelBehavior {
	var $name = 'CatalogItemCategoryLink';
	
	public function setup(Model $Model, $settings = array()) {
		if (is_numeric($settings)) {	
			$settings = array('catalog_item_category_id' => $settings);
		}
		$default = array();
	}
	
	public function getCatalogItemCategoryLinkTitle(Model $Model, $result) {
		if (isset($result[$Model->alias])) {
			$result = $result[$Model->alias];
		}
		return $result[$Model->displayField];	
	}
}