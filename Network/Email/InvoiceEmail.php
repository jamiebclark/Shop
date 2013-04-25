<?php
App::uses('ShopEmail', 'Shop.Network/Email');
class InvoiceEmail extends ShopEmail {
	var $model = 'Invoice';
	var $helpers = array(
		'Shop.Invoice',
		'Html',
		'Layout.DisplayText',
		'SuperEmail.Email',
		'Layout.AddressBook',
	);
	
/**
 * Sends an email to the customer letting them know their invoice has been successfully paid
 * 
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	function sendPaid($invoice) {
		return $this
			->emailFormat('copy')
			->viewVars(compact('invoice'))
			->subject('Your invoice has been successfully paid')
			->template('Shop.Invoice/paid')
			->sendResult($invoice);
	}
	
/**
 * Sends an email to admins letting them know an invoice has been successfully paid
 *
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	function sendAdminPaid($invoice) {
		return $this->send();
	}
}