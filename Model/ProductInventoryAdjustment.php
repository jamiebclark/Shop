<?php
/**
 * A combination of ProductInventory and OrderProduct
 *
 **/
App::uses('ShopAppModel', 'Shop.Model');
class ProductInventoryAdjustment extends ShopAppModel {
	public $name = 'ProductInventoryAdjustment';
	public $actsAs = ['Shop.ChangedFields'];
	public $order = ['ProductInventoryAdjustment.available' => 'DESC'];
	
	public $belongsTo = ['Shop.Product'];
	
	public $validate = [
		'product_id' => [
			'rule' => 'notBlank',
			'message' => 'Please select a product first',
		],
		'quantity' => [
			'rule' => 'notBlank',
			'message' => 'Please select an amount to add',
		],
		'available' => [
			'rule' => 'notBlank',
			'message' => 'Please let us know when the inventory will be available',
		]
	];
	
	public function beforeValidate($options = []) {
		if (!empty($this->data[$this->alias])) {
			$data =& $this->data[$this->alias];
		} else {
			$data =& $this->data;
		}

		$qty = 0;
		$productId = $data['product_id'];
		if (!empty($data['quantity'])) {
			$qty = $data['quantity'];
		}
		if (!empty($data['change_quantity'])) {
			$this->Product->id = $productId;
			$currentStock = $this->Product->field('stock');
			$qty = $data['change_quantity'] - $currentStock;
		} else {
			if (!empty($data['add_quantity'])) {
				$qty += $data['add_quantity'];
			}
			if (!empty($data['remove_quantity'])) {
				$qty -= abs($data['remove_quantity']);
			}
		}
		$data['quantity'] = $qty;

		return parent::beforeValidate($options);
	} 

	public function afterSave($created, $options = []) {
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

	
	public function beforeDelete($cascade = true) {
		$result = $this->read(null, $this->id);
		$result = $result[$this->alias];
		// Removes the stock from the Product's totals
		$this->Product->adjustStock($result['product_id'], -1 * $result['quantity']);
		return true;
	}

	public function findProductTotal($productId) {
		$result = $this->find('first', array(
			'fields' => 'SUM(quantity) AS total',
			'recursive' => -1,
			'conditions' => array(
				$this->escapeField('available') . ' <=' => date('Y-m-d H:i:s'),
				$this->escapeField('product_id') => $productId,
			),
			'group' => $this->escapeField('product_id'),
		));
		$total = 0;
		if (!empty($result)) {
			$total = $result[0]['total'];
		}
		return $total;
	}
	
	/*
	var $belongsTo = [
		'Product',
		'ProductOptionChoice1' => [
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_1'
		],
		'ProductOptionChoice2' => [
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_2'
		],
		'ProductOptionChoice3' => [
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_3'
		],
		'ProductOptionChoice4' => [
			'className' => 'ProductOptionChoice',
			'foreignKey' => 'product_option_choice_4'
		],
	];
	*/
}