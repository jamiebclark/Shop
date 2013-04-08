<?php
class OrderHelper extends AppHelper {
	var $name = 'Order';
	
	function getStatusClass($result) {
		$class = 'order';
		$order = !empty($result['Order']) ? $result['Order'] : $result;
		if ($order['canceled']) {
			$class .= '-canceled';
		} else {
			$class .= !empty($order['shipped']) ? '-shipped' : '-not-shipped';
			$class .= !empty($result['Invoice']['paid']) ? '-paid' : '-not-paid';
		}
		return $class;
	}
}