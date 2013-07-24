<?php
App::uses('Prefix', 'Shop.Lib');
App::uses('ModelViewHelper', 'Layout.View/Helper');

class OrderHelper extends ModelViewHelper {
	var $name = 'Order';
	var $modelPlugin = 'Shop';
	var $helpers = array(
		'Html',
		'Layout.Calendar',
		'Layout.AddressBook',
		'Layout.Layout',
		'Shop.Invoice',
	);
	
	public function title($result, $options = array()) {
		$options['text'] = 'Order #' . $result['id'];
		return parent::title($result, $options);
	}
	
	/*
	public function url($order) {
		return array(
			'controller' => 'orders',
			'action' => 'view',
			$order['id'],
			'plugin' => 'shop',
		) + Prefix::reset();
	}
	*/
	
	public function tracking($result) {
		$order = !empty($result['Order']) ? $result['Order'] : $result;
		$method = !empty($result['ShippingMethod']) ? $result['ShippingMethod'] : array();
		$text = '';
		$class = 'badge tracking';

		if (!empty($method)) {
			$class .= ' ' . strtolower(preg_replace('/[^A-Za-z0-9]/', '', $method['title']));
			$text = $method['title'];
		}
		if (!empty($order['tracking'])) {
			if (!empty($text)) {
				$text .= ': ';
			}
			$text .= $this->trackingLink($result);
		} else {
			$class .= ' blank';
		}
		return $this->Html->tag('span', $text, compact('class'));	
	}
	
	public function trackingLink($result) {
		$order = !empty($result['Order']) ? $result['Order'] : $result;
		$method = !empty($result['ShippingMethod']) ? $result['ShippingMethod'] : array();
		if (!empty($method['url'])) {
			return $this->Html->link(
				$order['tracking'], 
				trim($method['url']) . trim($order['tracking']), 
				array('title' => 'Track with ' . $method['title'], 'target' => '_blank')
			);
		} else {
			return $order['tracking'];
		}
	}
	
	public function status($result, $title = null) {
		$status = $this->getStatus($result);
		if (empty($title)) {
			$title = $status['title'];
		}
		return $this->Html->tag('span', $title, array(
			'class' => 'badge ' . $status['class']
		));
	}
	
	public function shipped($result) {
		$order = !empty($result['Order']) ? $result['Order'] : $result;
		if ($order['shipped']) {
			$title = 'Shipped ' . $this->Calendar->niceShort(
				$order['shipped'], array('time' => false));
		} else {
			$title = 'No shipped yet';
		}
		return $this->status($result, $title);
	}
	
	public function paid($result) {
		if (!($title = $this->Invoice->paid($result['Invoice']))) {
			$title = $this->Html->link('Not paid yet', array(
				'controller' => 'orders', 'action' => 'checkout', $result['Order']['id']
			));
		}
		return $this->status($result, $title);
	}

	public function shipping($result) {
		$order = isset($result['Order']) ? $result['Order'] : $result;
		return $this->AddressBook->address($order, array(
			'beforeField' => array(array('first_name', 'last_name'), 'company')
		));
	}
	
	public function getStatusClass($result) {
		$status = $this->getStatus($result);
		return $status['class'];
	}
	
	private function getStatus($result) {
		$class = 'order';
		$title = 'Ordered';
		$order = !empty($result['Order']) ? $result['Order'] : $result;
		if ($order['canceled']) {
			$class .= '-canceled';
			$title = 'Canceled';
		} else {
			if (!empty($order['shipped'])) {
				$title = 'Shipped';
				$class .= '-shipped';
			} else {
				$title = 'Not Shipped';
				$class .= '-not-shipped';
			}
			if (!empty($result['Invoice']['paid'])) {
				$title .= ' Paid';
				$class .= '-paid';
			} else {
				$title .= ' Not Paid';
				$class .= '-not-paid';
			}
		}
		return compact('title', 'class');
	}	
}