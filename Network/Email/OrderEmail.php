<?php
App::uses('ShopEmail', 'Shop.Network/Email');
class OrderEmail extends ShopEmail {
	var $model = 'Order';
	var $helpers = array(
		'Shop.Order',
		'Shop.CatalogItem',
		'Html',
		'Layout.AddressBook',
		'Layout.Calendar',
		'Layout.DisplayText',
		'Layout.Layout',
		'Layout.Table',
		'SuperEmail.Email',
	);

/**
 * Sends email to customer letting them know their order has been shipped
 *
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	public function sendShipped($order) {
		return $this
			->subject("Your order has shipped [#{$order['Order']['id']}]")
			->emailFormat('copy')
			->viewVars(compact('order'))
			->template('Shop.Order/shipped')
			->sendResult($order);
	}
	
/**
 * Sends email to customer letting them know their order has been paid
 *
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	public function sendPaid($order) {
		return $this
			->subject("Your order payment was successful [#{$order['Order']['id']}]")
			->emailFormat('copy')
			->viewVars(compact('order'))
			->template('Shop.Order/paid')
			->sendResult($order);	
	}
}