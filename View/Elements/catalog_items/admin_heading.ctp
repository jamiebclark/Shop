<?php
echo $this->Layout->menu(array(
	array('Store Products', array('action' => 'index')),
	array('Inactive Products', array('action' => 'inactive')),
	array('Product Order History', array('action' => 'totals'))
), array('class' => 'sub-tab-menu', 'currentSelect' => true));
?>
