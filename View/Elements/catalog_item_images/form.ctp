<?php
$add = !$this->Html->value('ProductImage.id');

if ($add) {
	$crumbs = array(
		'Add Image',
	);
} else {
	$crumbs = array(
		array('Image', array('action' => 'view', $this->Html->value('ProductImage.id'))),
		array('Edit'),
	);
}
echo $this->element('product_images/admin_heading', compact('crumbs'));

echo $this->Form->create('ProductImage', array('type' => 'file'));
echo $this->Form->inputs(array(
	'id',
	'product_id',
	'add_file' => array('type' => 'file')
));
echo $this->FormLayout->submit($add ? 'Add Image' : 'Update Image');
echo $this->Form->end();
?>