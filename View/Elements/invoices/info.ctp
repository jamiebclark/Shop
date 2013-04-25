<?php
if (!empty($invoice)) {
	$invoiceInfo = (!empty($invoice['Invoice'])) ? $invoice['Invoice'] : $invoice;
} else {
	$invoice = array();
}
$amount = $this->DisplayText->cash($invoiceInfo['amt']);
if (!empty($invoiceInfo['recur'])) {
	$amount .= ' every month for ' . number_format($invoiceInfo['recur']) . ' months';
}

$info = array(
	'Invoice ID#' => $invoiceInfo['id'],
	'Related' => $this->Invoice->relatedLink($invoice),
	'Amount' => $amount,
	'Paid' => $this->Calendar->niceShort($invoiceInfo['paid'], array(
		'empty' => $this->Html->tag(
			'font', 
			'UNPAID', 
			array('class' => 'negative')
		)
	)),
	'Created' => $this->Calendar->niceShort($invoiceInfo['created']),
	'Last Modified' => $this->Calendar->niceShort($invoiceInfo['modified']),
);
echo $this->Grid->col('1/2');
echo $this->Html->tag('h2', 'Invoice Information');
echo $this->Layout->infoTable($info);

echo $this->Grid->colContinue('1/2');
echo $this->Html->tag('h2', 'Customer Information');
$info = array(
	'Name' => !empty($invoice['User']['id']) ? $this->User->thumbNameLink($invoice['User'], array('dir' => 'tiny')) : $invoiceInfo['first_name'] . ' ' . $invoiceInfo['last_name'],
	'Address' => $this->Contact->location($invoiceInfo),
	'Email' => $this->Contact->email($invoiceInfo['email']),
	'Phone' => $this->Contact->phone($invoiceInfo['phone']),
);
echo $this->Layout->infoTable($info);

echo $this->Grid->colClose(true);
