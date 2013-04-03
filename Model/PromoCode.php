<?php
class PromoCode extends ShopAppModel {
	var $name = 'PromoCode';
	var $hasMany = array('Shop.OrdersPromoCode');
	var $hasAndBelongsToMany = array('Shop.Order');
	
	function beforeValidate() {
		$data =& $this->getData();
		$data['code'] = $this->formatCode($data['code']);
		
		//Makes sure they entered a valid percent
		if ($data['pct'] > 1) {
			$data['pct'] /= 100;
		}
		
		return true;
	}
	
	private function formatCode($code) {
		return strtoupper(str_replace(' ', '', $code));
	}
	
	public function findActiveCode($code) {
		return $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias . '.code LIKE' => $code,
				$this->alias . '.active' => 1,
				$this->alias . '.started IS NULL OR ' . $this->alias . '.started <= NOW()',
				$this->alias . '.stopped IS NULL OR ' . $this->alias . '.stopped >= NOW()',
			)
		));
	}
}
