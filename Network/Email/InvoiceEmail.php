<?php
App::uses('ShopEmail', 'Shop.Network/Email');
class InvoiceEmail extends ShopEmail {
	var $model = 'Invoice';
	var $helpers = array(
		'Shop.Invoice',
		'Html',
		'Layout.AddressBook',
		'Layout.Calendar',
		'Layout.Layout',
		'Layout.DisplayText',
		'SuperEmail.Email',

	);
	
/**
 * Sends an email to the customer letting them know their invoice has been successfully paid
 * 
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	function sendPaid($invoice) {
		$this->emailFormat('copy')
			->viewVars(compact('invoice'))
			->subject("Online payment successful: {$invoice['Invoice']['model_title']} #{$invoice['Invoice']['model_id']}")
			->template('Shop.Invoice/paid', 'Shop.default')
			->sendResult($invoice);
	}
	
/**
 * Sends an email to admins letting them know an invoice has been successfully paid
 *
 * @param array $invoice An Invoice model result
 * @return CakeEmail send
 **/
	function sendAdminPaid($invoice) {
		$emails = array_map('trim', explode(',', COMPANY_ADMIN_EMAILS));
		$Email = $this->to($emails);
		$Email->emailFormat('copy');
		$Email->viewVars(compact('invoice'));
		$Email->template('Shop.Invoice/admin_paid', 'Shop.default');
		$Email->subject(sprintf('Online payment successful for %s #%d', 
			$invoice['Invoice']['model_title'],
			$invoice['Invoice']['model_id']
		));
		return $Email->send();
	}
}