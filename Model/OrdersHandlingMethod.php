<?php
class OrdersHandlingMethod extends ShopAppModel {
	var $name = 'OrdersHandlingMethod';
	var $actsAs = array('Shop.BlankDelete' => array('and' => array('amt', 'pct')));
	var $belongsTo = array('Shop.HandlingMethod', 'Shop.Order');
	
/**
 * Removes de-activated or deleted handling rules
 *
 **/
	function removeUnused() {
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
