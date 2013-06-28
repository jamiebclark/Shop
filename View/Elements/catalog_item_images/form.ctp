<?php
$add = !$this->Html->value('ProductImage.id');
echo $this->Form->create(null, array('type' => 'file', 'class' => 'form-horizontal'));
echo $this->Form->inputs(array(
	'id',
	'add_file' => array('type' => 'file', 'helpBlock' => 'Select an image from your computer')
	'catalog_item_id' => array('helpBlock' => 'Select the which product this image represents'),
));
echo $this->FormLayout->end($add ? 'Add Image' : 'Update Image');
