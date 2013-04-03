<?php
class OrdersPromoCode extends ShopAppModel {
	var $name = 'OrdersPromoCode';
	var $belongsTo = array('Shop.PromoCode', 'Shop.Order');	var $actsAs = array('Shop.BlankDelete' => array('AND' => 'code'));
	
	function beforeValidate() {
		$data =& $this->getData();
		$unset = false;
		//Adding a new Promo
		if (empty($data['id']) && !empty($data['code'])) {
			$promoCode = $this->PromoCode->findActiveCode($data['code']);
			if (!empty($promoCode)) {
				$data['product_promo_id'] = $promoCode['PromoCode']['id'];
				$data['title'] = $promoCode['PromoCode']['title'];
				$data['amt'] = $promoCode['PromoCode']['amt'];
				$data['pct'] = $promoCode['PromoCode']['pct'];
				$data['code'] = $promoCode['PromoCode']['code'];

				if (!empty($data['order_id'])) {
					$result = $this->find('first', array(
						'conditions' => array(
							$this->alias . '.order_id' => $data['order_id'],
							$this->alias . '.product_promo_id' => $promoCode['PromoCode']['id'],
						)
					));
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
			$this->data = array();
		}
		return true;
	}
}
