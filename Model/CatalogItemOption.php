<?php
class CatalogItemOption extends ShopAppModel {
	var $name = 'CatalogItemOption';

	var $hasMany = array(
		'ProductOptionChoice' => array(
			'className' => 'Shop.ProductOptionChoice',
			'dependent' => true,
		)
	);
	
	var $belongsTo = array('Shop.CatalogItem');
	
	/**
	 * Finds the possible option choices, using the index as keys
	 * @param int $catalogItemId 
	 * @return array Array of possible choices with index as keys
	 */
	function findCatalogItemIndexes($catalogItemId) {
		$indexes = array();
		$choices = $this->ProductOptionChoice->find('all', array(
			'fields' => '*',
			'link' => array('Shop.' . $this->alias),
			'conditions' => array($this->alias . '.catalog_item_id' => $catalogItemId),
			'order' => $this->alias . '.index',
		));
		foreach ($choices as $choice) {
			$indexes[$choice[$this->alias]['index']][$choice['ProductOptionChoice']['id']] = $choice['ProductOptionChoice']['title'];
		}
		return $indexes;
	}
	
	function findCatalogItemOptions($catalogItemId) {
		$catalogItemOptions = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'CatalogItemOption.catalog_item_id' => $catalogItemId,
			)
		));
		
		$unlimited = $this->CatalogItem->find('count', array(
			'conditions' => array(
				'CatalogItem.id' => $catalogItemId,
				'CatalogItem.unlimited' => 1
			)
		));
		$catalogItemOptions = array();
		$ids = array();
		foreach ($catalogItemOptions as $key => $catalogItemOption) {
			$ids[$key] = $catalogItemOption['CatalogItemOption']['id'];
			$index = $catalogItemOption['CatalogItemOption']['index'];
			if (!isset($products)) {
				//Finds inventory for the first level of choices
				$products = array(
					$ids[$key] => $this->CatalogItem->Product->find('list', array(
					'fields' => array(
						'Product.product_option_choice_id_' . $index,
						'Product.stock',
					),
					'link' => array('Shop.CatalogItem'),
					'conditions' => array('CatalogItem.catalog_item_id' => $catalogItemId)
				)));
			}
		}
		$keyLink = array_flip($ids);
		
		$choices = $this->ProductOptionChoice->find('list', array(
			'fields' => array(
				'ProductOptionChoice.id', 'ProductOptionChoice.title', 'ProductOptionChoice.catalog_item_option_id',
			),
			'conditions' => array('ProductOptionChoice.catalog_item_option_id' => $ids)
		));
		foreach ($choices as $optionId => $choice) {
			if (!$unlimited && isset($products[$optionId])) {
				$advancedChoice = array();
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
