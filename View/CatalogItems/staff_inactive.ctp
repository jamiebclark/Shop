<?php
echo $this->Layout->defaultHeader(null, null, array(
	'title' => 'Inactive Store Products',
));
echo $this->element('catalog_items/admin_nav');
echo $this->element('catalog_items/admin_list');
