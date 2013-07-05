<?php
echo $this->element('orders/admin_heading', array(
	'crumbs' => array('Totals'),
));

$this->Table->reset();
foreach ($totals as $year => $total) {
	$this->Table->cell(
		$this->DisplayText->cash($total['total']),
		$year
	);
	/*
	$info = array();
	foreach ($total['day'] as $day => $total) {
		$info[date('n j', strtotime($day))] = $this->DisplayText->cash($total);
	}
	$this->Table->cell($this->Layout->infoTable($info), $year);
	*/
}
$this->Table->rowEnd();
echo $this->Table->output();
?>