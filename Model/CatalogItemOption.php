<?php
class CatalogItemOption extends ShopAppModel {
	public $name = 'CatalogItemOption';

	public $actsAs = [
		'Shop.BlankDelete' => ['title'],
		'Shop.FieldOrder' => [
			'orderField' => 'index',
			'subKeyFields' => ['catalog_item_id']
		],
		'Layout.Removable',
	];
	
	public $hasMany = [
		'ProductOptionChoice' => [
			'className' => 'Shop.ProductOptionChoice',
			'dependent' => true,
		]
	];
	public $belongsTo = ['Shop.CatalogItem'];
	
	public $validate = [
		'title' => [
			'rule' => 'notBlank',
			'message' => 'Please give this option a title',
		]
	];
	
	/*
	function beforeSave($options = []) {
		$data =& $this->getData();
		//If no index is set, add it to the bottom
		if (empty($data['index'])) {
			$index = $this->find('count', [
				'recursive' => -1,
				'conditions' => ['catalog_item_id' => $data['catalog_item_id']]
			]);
			if (empty($data['id'])) {
				$index++;
			}
			$data['index'] = $index;
		}
		return parent::beforeSave($options);
	}
	*/
	
/**
 * Finds the possible option choices, using the index as keys
 *
 * @param int $catalogItemId 
 * @param string $key The field used to index the returned array
 * @return array Array of possible choices with index as keys
 */
	public function findCatalogItemIndexes($catalogItemId, $keyField = 'index') {
		$indexes = [];
		$choices = $this->ProductOptionChoice->find('all', [
			'fields' => '*',
			'link' => ['Shop.' . $this->alias],
			'conditions' => [$this->alias . '.catalog_item_id' => $catalogItemId],
			'order' => $this->alias . '.index',
		]);
		foreach ($choices as $choice) {
			$indexes[$choice[$this->alias][$keyField]][$choice['ProductOptionChoice']['id']] = $choice['ProductOptionChoice']['title'];
		}
		return $indexes;
	}
	
/**
 * Finds a multi-dimensional result of options and choices, using the option titles as keys
 *
 * @param int $catalogItemId 
 * @return array Array of possible choices with title as keys
 */
	public function findCatalogItemList($catalogItemId) {
		return $this->findCatalogItemIndexes($catalogItemId, 'title');
	}
	
	function findCatalogItemOptions($catalogItemId) {
		$catalogItemOptions = $this->find('all', [
			'recursive' => -1,
			'conditions' => [
				'CatalogItemOption.catalog_item_id' => $catalogItemId,
			]
		]);
		
		$unlimited = $this->CatalogItem->find('count', [
			'conditions' => [
				'CatalogItem.id' => $catalogItemId,
				'CatalogItem.unlimited' => 1
			]
		]);
		$catalogItemOptions = [];
		$ids = [];
		foreach ($catalogItemOptions as $key => $catalogItemOption) {
			$ids[$key] = $catalogItemOption['CatalogItemOption']['id'];
			$index = $catalogItemOption['CatalogItemOption']['index'];
			if (!isset($products)) {
				//Finds inventory for the first level of choices
				$products = array(
					$ids[$key] => $this->CatalogItem->Product->find('list', [
					'fields' => [
						'Product.product_option_choice_id_' . $index,
						'Product.stock',
					],
					'link' => ['Shop.CatalogItem'],
					'conditions' => ['CatalogItem.catalog_item_id' => $catalogItemId]
				]));
			}
		}
		$keyLink = array_flip($ids);
		
		$choices = $this->ProductOptionChoice->find('list', [
			'fields' => [
				'ProductOptionChoice.id', 'ProductOptionChoice.title', 'ProductOptionChoice.catalog_item_option_id',
			],
			'conditions' => ['ProductOptionChoice.catalog_item_option_id' => $ids]
		]);
		foreach ($choices as $optionId => $choice) {
			if (!$unlimited && isset($products[$optionId])) {
				$advancedChoice = [];
				foreach ($choice as $value => $name) {
					$stock = !empty($products[$optionId][$value]) ? $products[$optionId][$value] : 0;
					$disabled = $stock <= 0;
					if ($disabled) {
						$name .= ' - Out of stock';
					} else {
						//$name .= ' (' . number_format($stock) . ' in stock)';
					}
					$advancedChoice[] = compact('name', 'value', 'disabled');
				}
				$choice = $advancedChoice;
			}
			$catalogItemOptions[$keyLink[$optionId]]['ProductOptionChoice'] = $choice;
		}
		return $catalogItemOptions;
	}
}
