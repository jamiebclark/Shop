<h1>Order Totals per Year</h1>
<h3><?php
	echo $this->Html->link('Per-product totals', array(
		'controller' => 'catalog_items', 'action' => 'totals',
	));
	?></h3>

<?php
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
