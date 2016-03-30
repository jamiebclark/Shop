<?php
echo $this->Form->create('Invoice', ['url' => ['action' => 'index']]);
echo $this->FormLayout->searchInput('Invoice.id', [
	'placeholder' => 'Search by Invoice #',
]);
echo $this->Form->end();