<?php
$result = $invoice;
if (!empty($invoice['Invoice'])) {
	$invoice = $invoice['Invoice'];
}
$payment = array(
	'Invoice ID#' => $invoice['id'],
	'Related' => $this->Invoice->relatedLink($invoice),
	'Amount' => $this->Invoice->amount($invoice),
	'Paid' => $this->Invoice->paid($result),
	'Created' => $this->Calendar->niceShort($invoice['created']),
	'Last Modified' => $this->Calendar->niceShort($invoice['modified']),
);
$customer = array(
	'Name' => "{$invoice['first_name']} {$invoice['last_name']}",
	'Address' => $this->AddressBook->location($invoice),
	'Email' => $this->AddressBook->email($invoice['email']),
	'Phone' => $this->AddressBook->phone($invoice['phone']),
);
if (isset($result['PaymentMethod'])) {
	$payment['Paid By'] = $result['PaymentMethod']['title'];
}

?>
<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">Payment Information</span></div>
	<?php echo $this->Layout->infoTable($payment); ?>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">Customer Information</span></div>
	<?php echo $this->Layout->infoTable($customer); ?>
</div>
