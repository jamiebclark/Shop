<?php
$keys = array(
	'Ordered' => array(0, 0),
	'Paid' => array(0, 1),
	'Shipped Not Paid' => array(1, 0),
	'Shipped and Paid' => array(1, 1),
	'Cancelled' => false
);
$this->Table->reset();
$cells = array();
foreach ($keys as $label => $key) {
	if (is_array($key)) {
		$cancelled = 0;
		list($shipped, $paid) = $key;
		$class = ($shipped ? 'shipped' : 'notShipped') . ($paid ? 'Paid' : 'NotPaid');
	} else {
		$cancelled = true;
		list($shipped, $paid) = array(false, false);
		$class = 'cancelled';
	}
	$url = compact('shipped', 'paid', 'cancelled');
	$width = round(100 / count($keys)) . '%';
	$cells[] = array($this->Html->link($label, $url), null, null, null, compact('class', 'width'));
}

$this->Table->cells($cells, true);
echo $this->Table->table(array('class' => 'orders key'));
?>