<?php
class OrdersPromoCode extends ShopAppModel {
	public $name = 'OrdersPromoCode';
	public $belongsTo = ['Shop.PromoCode', 'Shop.Order'];
	public $actsAs = ['Shop.BlankDelete' => ['AND' => 'code']];
	
	protected $copyFields = ['title', 'amt', 'pct', 'code'];

	public function beforeValidate($options = []) {
		$data =& $this->getData();
		$unset = false;

		// Adding a new Promo
		if (empty($data['id']) && !empty($data['code'])) {
			$promoCode = $this->PromoCode->findActiveCode($data['code']);
			if (!empty($promoCode)) {
				$data['product_promo_id'] = $promoCode['PromoCode']['id'];
				$data = $this->getCopyData($promoCode, $data);

				if (!empty($data['order_id'])) {
					$result = $this->find('first', [
						'conditions' => [
							$this->escapeField('order_id') => $data['order_id'],
							$this->escapeField('product_promo_id') => $promoCode['PromoCode']['id'],
						]
					]);
					if (!empty($result)) {
						$data['id'] = $result[$this->alias]['id'];
						$this->save($data);
						$unset = true;
					}
				}				
			} else {
				$unset = true;
			}
		} else {
			$unset = true;
		}
		
		if ($unset) {
			$this->data = [];
		}
		
		return parent::beforeValidate($options);
	}

	public function getCopyData($promoCode, $data = []) {
		if (!is_array($promoCode)) {
			$promoCode = $this->PromoCode->findActiveCode($promoCode);
		}
		if (!empty($promoCode['PromoCode'])) {
			$promoCode = $promoCode['PromoCode'];
		}
		foreach ($this->copyFields as $field) {
			$data[$field] = !empty($promoCode[$field]) ? $promoCode[$field] : null;
		}
		return $data;
	}
}