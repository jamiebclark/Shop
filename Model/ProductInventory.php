<?php
App::uses('ShopAppModel', 'Shop.Model');
class ProductInventory extends ShopAppModel {
	var $name = 'ProductInventory';
	
	var $hasMany = array(
		'ProductInventoryAdjustment' => array(
			'className' => 'Shop.ProductInventoryAdjustment',
			'dependent' => true,
		),
		'Shop.OrderProduct'
	);
	var $belongsTo = array('Shop.Product');
	var $recursive = 0;

	function beforeSave($options = array()) {
		$data =& $this->getData();
		if (empty($data['id']) && !empty($data['product_id'])) {
			unset($data['id']);
			$conditions = $data;
			foreach ($conditions as $key => $val) {
				if (empty($val)) {
					unset($conditions[$key]);
					$conditions[] = "$key IS NULL";
				}
			}
			$recursive = -1;
			$result = $this->find('first', compact('recursive', 'conditions'));
			if (!empty($result)) {
				$data['id'] = $result[$this->alias]['id'];
				$this->id = $data['id'];
				$this->save($data);
				return false;
			}
		}
		return parent::beforeSave($options);
	}
	function afterSave($created, $options = array()) {
		$id = $this->id;
		$result = $this->read(array('product_id'), $id);
		$productId = $result[$this->alias]['product_id'];
		$catalogItem = $this->Product->findCatalogItem($productId, array(
			'postContain' => array('CatalogItemPackageParent'),
		));
		
		if (!empty($product['Product'])) {
			$this->Product->updateStock($product['Product']['id']);
		}
		
		if (!empty($product['ProductPackageParent'])) {
			foreach ($product['ProductPackageParent'] as $productPackage) {
				$this->Product->updateStock($productPackage['product_parent_id']);
			}
		}
		return parent::afterSave($created);
	}
	
	function adjustQuantity($id, $adjustQuantity) {
		$this->updateAll(
			array('Product.stock' => 'Product.stock + ' . $adjustQuantity), 
			array('ProductInventory.id' => $id)
		);
		$this->read(null, $id);
		return $this->afterSave(false);
	}
	
	function rebuildQuantity($id) {
		$result = $this->ProductInventoryAdjustment->find('first', array(
			'fields' => 'SUM(ProductInventoryAdjustment.quantity) AS total_inventory',
			'link' => array('Shop.Product'),
			'conditions' => array('Product.id' => $id)
		));
		$totalInventory = !empty($result) ? $result[0]['total_inventory'] : 0;
		
		$result = $this->Product->OrderProduct->find('first', array(
			'fields' => 'SUM(OrderProduct.quantity) AS total_sold',
			'link' => array('Shop.Order', 'Shop.Product'),
			'conditions' => array(
				'Product.id' => $id,
				'Order.archived' => 1,
				'Order.canceled' => 0,
			)
		));
		$totalSold = !empty($result) ? $result[0]['total_sold'] : 0;
		$newQuantity = $totalInventory - $totalSold;
		$this->updateAll(array(
			$this->alias . '.quantity' => $newQuantity,
		), array(
			$this->alias . '.id' => $id
		));
		return $newQuantity;
	}
	
	function checkQuantity($productId, $quantity = 1, $conditions = array()) {
		//If the product is a package, check all the child elements in the package
		$productChildren = $this->Product->findPackageChildren($productId);
		if (!empty($productChildren)) {
//			debug($productChildren);
			foreach ($productChildren as $key => $productChild) {
				if (!$this->checkQuantity(
					$productChild['ProductChild']['id'], 
					$productChild['ProductPackageChild']['quantity'] * $quantity
				)) {
					//$this->invalidate('ProductChild.' . $key . '.quantity', 'Not 
					return false;
				}
			}
			return true;
		}
		
		$conditions = array_merge(array('Product.id' => $productId), $conditions);
		$result = $this->find('first', array(
			'fields' => '*', //$this->alias . '.quantity',
			'link' => array('Shop.Product' => array('type' => 'RIGHT')),
			'conditions' => array(
				'OR' => array($conditions, 'Product.unlimited = 1 AND Product.id = ' . $productId)
			)
		));
		if (empty($result[$this->alias]['quantity'])) {
			$result[$this->alias]['quantity'] = 0;
		}
		
		$checkQuantity = $result[$this->alias]['quantity'] - $quantity;
	
		if (!empty($result['Product']['unlimited'])) {
			return true;
		} else if (!empty($checkQuantity)) {
			return $checkQuantity > 0;
		} else {
			return false;
		}
	}

	function updateTitle($id) {
		$result = $this->read(null, $id);
		$title = $subTitle = '';
		if (!empty($result['Product']['title'])) {
			$title = $result['Product']['title'];
		}
		$i = 0;
		while(isset($result['ProductOptionChoice' . ++$i])) {
			if (!empty($result['ProductOptionChoice' . $i]['title'])) {
				if (!empty($subTitle)) {
					$subTitle .= ', ';
				}
				$subTitle .= $result['ProductOptionChoice' . $i]['title'];
			}
		}
		if (!empty($subTitle)) {
			$title .= ': ' . $subTitle;
		}
		$this->set('title', $title);
		return $this->save(null, array('callbacks' => false, 'validate' => false));
	}
}