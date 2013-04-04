<?php
echo $this->Layout->menu(array(
	array('Store Catalog Items', array('action' => 'index')),
	array('Inactive Catalog Items', array('action' => 'inactive')),
	array('Order History', array('action' => 'totals'))
), array('class' => 'sub-tab-menu', 'currentSelect' => true));
