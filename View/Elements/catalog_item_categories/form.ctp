<?php
$add = !$this->Html->value('CatalogItemCategory.id');

echo $this->Form->create('CatalogItemCategory');
echo $this->Form->inputs(array(
	'legend' => 'Category',
	'id',
	'parent_id' => array('class' => 'form-control select-collapse'),
	'title',
	'CatalogItem.CatalogItem' => array(
		'multiple' => 'checkbox',
		'options' => $catalogItems,
	)
));
echo $this->FormLayout->end($add ? 'Add New Category' : 'Update Category');