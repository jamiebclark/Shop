<?php
$add = !$this->Html->value('ProductCategory.id');
if ($add) {
	$crumbs = array(
		'Add Category',
	);
} else {
	$crumbs = array(
		array($this->Html->value('ProductCategory.title'), array('action' => 'view', $this->Html->value('ProductCategory.id'))),
		'Edit Category',
	);
}

echo $this->Form->create('ProductCategory');
echo $this->Form->inputs(array(
	'legend' => 'Product Category',
	'id',
	'parent_id',
	'title',
	'Product.Product' => array(
		'multiple' => 'checkbox',
		'options' => $products,
	)
));
echo $this->FormLayout->submit($add ? 'Add New Category' : 'Update Category');
echo $this->Form->end();
?>