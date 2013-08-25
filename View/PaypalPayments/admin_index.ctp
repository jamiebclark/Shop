<h2>PayPal Payments</h2>
<?php 
$this->Table->reset();
foreach ($paypalPayments as $paypalPayment) {
	$row = $paypalPayment['PaypalPayment'];
	$isComplete = $row['payment_status'] == 'Completed';
	
	$invoiceLink = null;
	if (!empty($row['invoice'])) {
		$invoiceLink = $this->Html->link($row['invoice'], array('controller' => 'invoices', 'action' => 'view', $row['invoice']));
	}
	$this->Table->cells(array(
		array($this->Calendar->niceShort($row['payment_date']), 'Date', 'payment_date'),
		array($invoiceLink, 'Invoice', 'invoice'),
		array($this->DisplayText->cash($row['mc_gross']), 'Amount', $row['mc_gross']),
		array("{$row['item_name']} {$row['item_number']}", 'Title'),
		array($this->Html->tag('span',
			$row['payment_status'],
			array('class' => 'badge ' . ($isComplete ? 'badge-success' : 'badge-warning'))
		), 'Status', 'payment_status'),
		array("{$row['first_name']} {$row['last_name']}", 'Name'),
		array($this->Html->link('Test Send', array('action' => 'test', $row['txn_id'])), 'Test'),
	), array('class' => $isComplete ? 'success' : 'warning'));
}
echo $this->Table->output(array('paginate' => true));