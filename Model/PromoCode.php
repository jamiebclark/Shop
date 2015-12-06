<?php
class PromoCode extends ShopAppModel {
	public $name = 'PromoCode';
	public $hasMany = ['Shop.OrdersPromoCode'];
	public $hasAndBelongsToMany = ['Shop.Order'];
	
	public function beforeValidate($options = []) {
		$data =& $this->getData();
		$data['code'] = $this->formatCode($data['code']);
		
		//Makes sure they entered a valid percent
		if ($data['pct'] > 1) {
			$data['pct'] /= 100;
		}
		
		return true;
	}
	
	private function formatCode($code) {
		return trim(strtoupper(str_replace(' ', '', $code)));
	}
	
	public function findActiveCode($code) {
		return $this->find('first', [
			'recursive' => -1,
			'conditions' => [
				'OR' => [
					$this->escapeField('code') . ' LIKE' => $this->formatCode($code),
					$this->escapeField() => $code,
				],
				$this->escapeField('active') => 1,
				$this->escapeField('started') . ' IS NULL OR ' . $this->escapeField('started') . ' <= NOW()',
				$this->escapeField('stopped') . ' IS NULL OR ' . $this->escapeField('stopped') . ' >= NOW()',
			]
		]);
	}
}
