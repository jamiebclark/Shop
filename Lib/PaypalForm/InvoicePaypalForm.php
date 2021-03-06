<?php
App::uses('PaypalForm', 'Shop.Lib');
App::uses('Router', 'Config');

class InvoicePaypalForm extends PaypalForm {
	public function getResultTranslateKeys($result, $translateKeys = []) {
		return array_merge($translateKeys, [
			'first_name',
			'last_name',
			'addline1' => 'address1',
			'addline2' => 'address2',
			'city',
			'state',
			'zip',
			'country',
			'phone' => 'day_phone',
			'phone' => 'night_phone',
			'email',
			'amt' => 'amount',
			'id' => 'invoice',
		]);
	}

	public function setModelResult($result, $translateKeys = []) {
		parent::setModelResult($result, $translateKeys);

		// Add additional Invoice settings
		$settings = [];
		$url = [
			'controller' => 'invoices',
			'plugin' => 'shop',
			'action' => 'view',
			$result['id'],
		];
		
		if (!empty($result['model_title'])) {
			$settings['item_name'] = "{$result['model_title']} #{$result['model_id']}";
			$settings['item_number'] = $result['model_id'];
			$url['action'] = 'model_view';
		} else {
			$settings['item_name'] = !empty($result['title']) ? $result['title'] : 'Invoice';
			$settings['item_number'] = $result['id'];
		}

		if (!empty($this->request->prefix)) {
			$url[$this->request->prefix] = false;
		}

		$cancelUrl = $url + ['?' => ['cancel_pending' => 1]];

		$settings['return'] = Router::url($url, true);
		$settings['cancel_return'] = Router::url($cancelUrl, true);

		// Recurring payments
		$recurUnit = 'M';
		if (!empty($result['recur'])) {
			$settings['amt'] = false;

			$settings['cmd'] = '_xclick-subscriptions';
			$settings['model_title'] = 'Subscription Payment';
			$settings['a3'] = $result['amt'];
			$settings['p3'] = 1;					//Once
			$settings['t3'] = $recurUnit;			//Every Month
			$settings['src'] = 1;					//Do not recur when it completes the cycle
			$settings['srt'] = $result['recur'];	//Repeat this many times
		}
		$this->set($settings, true);
	}
}