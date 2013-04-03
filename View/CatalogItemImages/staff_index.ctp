<?php
echo $this->element('product_images/staff_heading');

echo $this->Html->tag('h1', 'Product Images');
echo $this->Layout->headerMenu(array(
	array('Add a new image', array('action' => 'add')),
));

$this->Table->reset();
foreach ($productImages as $productImage) {
	$url = array(
		'action' => 'view',
		$productImage['ProductImage']['id'],
	);
	$this->Table->cells(array(
		array(
			$this->Product->thumb($productImage['Product'], array(
				'dir' => 'thumb', 
				'url' => $url
			)),
			'Image',
			null,
			null,
			array('width' => 40)
		), array(
			$this->Html->link($productImage['Product']['title'], $url, array('class' => 'secondary')),
			'Product',
		), array(
			$this->Layout->actionMenu(array('view', 'edit', 'delete', 'move_up', 'move_down'), compact('url')),
			'Actions',
		)
	), true);
}
echo $this->Table->table(array('paginate'));
?>