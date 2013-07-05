<?php
echo $this->Layout->defaultHeader();
 
echo $this->Table->tableSortMenu(array(
	array('Last Added', 'Invoice.created', 'desc'),
	array('Last Updated', 'Invoice.modified', 'desc'),
));

$this->Table->reset();
foreach ($invoices as $invoice) {
	$invoice = $invoice['Invoice'];
	debug($invoice);

	$class = !empty($invoice['paid']) ? 'success' : null;
	$this->Table->checkbox($invoice['id']);
	$this->Table->cells(array(
		array($this->Invoice->link($invoice), 'Invoice #', 'id'),
		array(
			$this->Invoice->amount($invoice), 
			'Amount', 
			'amt',
			array('class' => 'cash')
		),
		array($this->Invoice->paid($invoice, 'Paid', 'paid'), 'Paid', 'paid'),
		array($this->AddressBook->location($invoice, array('beforeField' => array('name'))), 'Billing Info'),
		array($this->Invoice->relatedLink($invoice, array(
			'class' => 'secondary',
			'target' => '_blank',
		)), 'Related To:'),
		array($this->Calendar->niceShort($invoice['created']), 'Created', 'created'),
		
		array($this->Invoice->actionMenu(array('view', 'edit', 'delete'), $invoice), 'Actions'),
	), compact('class'));
}
echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete'),
));
