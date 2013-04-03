<?php
echo $this->Form->create('Order', array('action' => 'index'));
echo $this->FormLayout->searchInput('Order.id', array(
	'placeholder' => 'Search by Order #',
));
echo $this->Form->end();
?>