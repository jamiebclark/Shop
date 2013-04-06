<?php
/**
 * A combination of ProductInventory and OrderProduct
 *
 **/
class ProductInventoryAdjustment extends ShopAppModel {
	var $name = 'ProductInventoryAdjustment';
	var $actsAs = array('Shop.ChangedFields');
	var $order = 'Shop.ProductInventoryAdjustment.available DESC';
	
	var $belongsTo = array('Shop.Product');
	
	
	function afterSave($created) {
		if ($created || in_array('quantity', $this->changedFields)) {
			$result = $this->findById($this->id);
			$quantity = $result[$this->alias]['quantity'];
			if (!empty($this->old[$this->alias]['quantity'])) {
				$quantity -= $this->old[$this->alias]['quantity'];
			}
			$this->Product->updateStock($result[$this->alias]['product_id']);
		}
		return parent::afterSave($created);
	}

	
	function beforeDelete() {
		$result = $this->read(null, $this->id);
		$result = $result[$this->alias];
		//Removes the stock from the Product's totals
		$this->Product->adjustStock($result['product_id'], -1 * $result['quantity']);
		return true;
	}

	function findProductTotal($productId) {
		$result = $this->find('first', array(
			'fields' => 'SUM(quantity) AS total',
			'recursive' => -1,
			'conditions' => array(
				$this->alias . '.available <=' => date('Y-m-d H:i:s'),
				$this->alias . '.product_id' => $productId,
			),
			'group' => $this->alias . '.product_id',
		));
		$total = 0;
		if (!empty($result)) {
			$total = $result[0]['total'];
		}
		return $total;
	}
	
	/*
	var $belongsTo = array(
		'Product',
		'ProductOptionChoice1' => array(
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_1'
		),
		'ProductOptionChoice2' => array(
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_2'
		),
		'ProductOptionChoice3' => array(
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_3'
		),
		'ProductOptionChoice4' => array(
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_4'
		),
	);
	*/
}