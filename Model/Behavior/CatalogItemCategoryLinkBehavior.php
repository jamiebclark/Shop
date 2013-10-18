<?php
class CatalogItemCategoryLinkBehavior extends ModelBehavior {
	var $name = 'CatalogItemCategoryLink';
	
	private $CatalogItemCategoryLink;
	
	public function setup(Model $Model, $settings = array()) {
		if (is_numeric($settings)) {	
			$settings = array('catalog_item_category_id' => $settings);	//The parent Catalog Item Category
		}
		$default = array(
			'catalog_item_category_id' => null,	// Parent Category ID
		);
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array) $settings);
		
		if (empty($this->CatalogItemCategoryLink)) {
			$this->CatalogItemCategoryLink = ClassRegistry::init('Shop.CatalogItemCategoryLink', true);
		}
		$this->bindCatalogItemCategoryLink($Model);
	}
	
	private function bindCatalogItemCategoryLink(Model $Model) {
		$Model->bindModel(array(
			'hasMany' => array(
				'CatalogItemCategoryLink' => array(
					'className' => 'Shop.CatalogItemCategoryLink',
					'foreignKey' => 'model_id',
					'conditions' => array('CatalogItemCategoryLink.model' => $Model->alias),
					'dependent' => true,
				)
			)
		), false);
		$this->CatalogItemCategoryLink->bindModel(array(
			'belongsTo' => array(
				$Model->alias => array(
					'foreignKey' => 'model_id',
					'conditions' => array('CatalogItemCategoryLink.model' => $Model->alias),
				)
			)
		), false);
	}
	
	public function afterSave(Model $Model, $created, $options = array()) {
		$this->updateCatalogItemCategoryLink($Model, $Model->id);
		return parent::afterSave($Model, $created, $options);
	}
	
	public function updateCatalogItemCategoryLink(Model $Model, $id) {
		$parentCategoryId = $this->settings[$Model->alias]['catalog_item_category_id'];
		$result = $Model->find('first', array('recursive' => -1, 'conditions' => array($Model->escapeField($Model->displayKey) => $id)));
		$link = $this->CatalogItemCategoryLink->findByModelId($Model->alias, $id);
		$linkId = $categoryId = null;
		if (!empty($link['CatalogItemCategoryLink'])) {
			$link = $link['CatalogItemCategoryLink'];
			$linkId = $link['id'];
			$categoryId = $link['catalog_item_category_id'];
		}
		return $this->CatalogItemCategoryLink->saveAll(array(
			'CatalogItemCategoryLink' => array(
				'id' => $linkId,
				'model' => $Model->alias,
				'model_id' => $id,
			),
			'CatalogItemCategory' => array(
				'id' => $categoryId,
				'parent_id' => $parentCategoryId,
				'title' => $Model->getCatalogItemCategoryLinkTitle($result),
			),
		));
	}
	
	// Returns the Category title based on the link
	// Could be extended to be something other than displayField
	public function getCatalogItemCategoryLinkTitle(Model $Model, $result) {
		if (isset($result[$Model->alias])) {
			$result = $result[$Model->alias];
		}
		return $result[$Model->displayField];	
	}
	
	
	//Finds the associated Catalog Item Category
	public function findCatalogItemCategory(Model $Model, $id = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		return $this->CatalogItemCategoryLink->CatalogItemCategory->find('first', array(
			'link' => array('Shop.CatalogItemCategoryLink' => array($Model->alias)),
			'conditions' => array($Model->escapeField($Model->primaryKey) => $id),
		));
	}
}