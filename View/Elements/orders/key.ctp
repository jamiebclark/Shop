<?php
$keys = array(
	'Ordered' => array(0, 0),
	'Paid and Not Shipped' => array(0, 1),
	'Not Paid and Shipped' => array(1, 0),
	'Shipped and Paid' => array(1, 1),
	'Canceled' => false
);
$this->Table->reset();
$cells = array();
$width = round(100 / count($keys)) . '%';
foreach ($keys as $label => $key) {
	$class = 'order';
	if (is_array($key)) {
		$canceled = 0;
		list($shipped, $paid) = $key;
		$class .= ($shipped ? '-shipped' : '-not-shipped') . ($paid ? '-paid' : '-not-paid');
	} else {
		$canceled = true;
		list($shipped, $paid) = array(false, false);
		$class .= '-canceled';
	}
	$url = compact('shipped', 'paid', 'canceled');
	$cells[] = array($this->Html->link($label, $url), compact('class', 'width'));
}
$this->Table->cells($cells, true);
echo $this->Table->output(array('class' => 'orders key'));
