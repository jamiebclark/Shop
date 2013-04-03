<?php
echo $this->element('products/staff_heading');
echo $this->element('products/admin_heading');
echo $this->Layout->defaultHeader(null, null, array(
	'title' => 'Online Store Products',
));

echo $this->element('products/admin_list');
?>