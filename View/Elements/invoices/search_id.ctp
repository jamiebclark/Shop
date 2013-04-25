<?php
echo $this->Form->create('Invoice', array('action' => 'index'));
echo $this->FormLayout->searchInput('Invoice.id', array(
	'placeholder' => 'Search by Invoice #',
));
echo $this->Form->end();
?>