<?php
echo $this->Form->create('Order', ['url' => ['action' => 'index']]);
echo $this->FormLayout->searchInput('Order.id', [
	'placeholder' => 'Search by Order #',
]);
echo $this->Form->end();