<?php
$this->Asset->css('full_width');

echo $this->Html->div('span-16');
echo $this->Html->tag('h1', 'Invoices', array('class' => 'topTitle'));
echo "</div>\n";
echo $this->Html->div('span-8 last', $this->element('invoices/search_id'));

echo "<hr/>\n";

echo $this->Layout->headerMenu(array(
	array('Add Invoice', array('action' => 'add'))
));
echo $this->element('find_filter/heading');
 
echo $this->Layout->tableSortMenu(array(
	array('Last Added', 'Invoice.created', 'desc'),
	array('Last Updated', 'Invoice.modified', 'desc'),
));

$this->Table->reset();
foreach ($invoices as $invoiceInfo) {
	$url = array('action' => 'view', $invoiceInfo['Invoice']['id']);
	
	if (!empty($invoiceInfo['Invoice']['paid'])) {
		$paidCell = $this->Calendar->niceShort($invoiceInfo['Invoice']['paid'], array('class' => 'positive'));
		if (!empty($invoiceInfo['Invoice']['paypal_payment_id'])) {
			$paidCell .= '<br/>' . $this->Html->link('PayPal', array(
				'controller' => 'paypal_payments',
				'action' => 'view',
				$invoiceInfo['Invoice']['paypal_payment_id'],
			));
		}
	} else {
		$paidCell = $this->Html->tag('font', 'NOT PAID', array('class' => 'negative'));
	}
	$this->Table->cells(array(
		array($this->Html->link('Invoice #' . $invoiceInfo['Invoice']['id'], $url), 'Invoice #', 'id'),
		array($this->DisplayText->cash($invoiceInfo['Invoice']['amt'], 'Amount')),
		array($paidCell, 'Paid', 'paid'),
		array($this->Contact->location($invoiceInfo['Invoice'], array('beforeField' => array('name'))), 'Billing Info'),
		array($this->Invoice->relatedLink($invoiceInfo, array(
			'class' => 'secondary',
			'target' => '_blank',
		)), 'Related To:'),
		array($this->Calendar->niceShort($invoiceInfo['Invoice']['created']), 'created', true),
		
		array($this->Layout->actionMenu(array('view', 'edit', 'delete'), compact('url'), 'Actions')),
	), true);
}
echo $this->Table->table(array('paginate'));

?>