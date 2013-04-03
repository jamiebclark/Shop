<?php
echo $this->element('orders/staff_heading');

echo $this->Html->div('span-6 prepend-12', $this->element('invoices/search_id'));
echo $this->Html->div('span-6 last', $this->element('orders/search_id'));

echo $this->Html->tag('h1', 'Online Store Orders');
echo $this->Layout->headerMenu(array(
	array('Add Order', array('action' => 'add'))
));
echo $this->element('find_filter/heading');
echo $this->element('orders/admin_list');
?>