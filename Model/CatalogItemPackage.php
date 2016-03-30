<?php
class CatalogItemPackage extends ShopAppModel {
	public $name = 'CatalogItemPackage';
	public $actsAs = [
		'Shop.BlankDelete' => ['or' => [
			'catalog_item_child_id', 'quantity'
		]]
	];
	
	public $belongsTo = [
		'CatalogItemParent' => [
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_item_parent_id',
		],
		'CatalogItemChild' => [
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_item_child_id',
		]
	];

	public $validate = [
		'catalog_item_parent_id' => [
			'rule' => 'notBlank',
			'message' => 'Please select a catalog item parent',
		],
		'catalog_item_child_id' => [
			'rule' => 'notBlank',
			'message' => 'Please select a catalog item child',
		],
		'quantity' => [
			'rule' => 'notBlank',
			'message' => 'Please select a quantity',
		]
	];
	
	public function afterSave($created, $options = []) {
		$result = $this->read(null, $this->id);
		
		$this->CatalogItemParent->save([
			'id' => $result[$this->alias]['catalog_item_parent_id'],
			'is_package' => 1,
			'unlimited' => 1,
		]);
		
		return parent::afterSave($created);
	}
}