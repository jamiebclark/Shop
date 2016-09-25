<?php
class OrdersHandlingMethod extends ShopAppModel {
	public $name = 'OrdersHandlingMethod';
	public $actsAs = array('Shop.BlankDelete' => array('and' => array('amt', 'pct')));
	public $belongsTo = array('Shop.HandlingMethod', 'Shop.Order');
	
/**
 * Removes de-activated or deleted handling rules
 *
 **/
	public function removeUnused() {
		$obsolete = $this->find('list', array(
			'link' => array('Shop.HandlingMethod'),
			'conditions' => array(
				'OR' => array('HandlingMethod.active' => 0, 'HandlingMethod.id' => null)
			)
		));
		if (!empty($obsolete)) {
			return $this->deleteAll(array('OrdersHandlingMethod.id' => array_keys($obsolete)));
		}
		return null;	
	}
}
