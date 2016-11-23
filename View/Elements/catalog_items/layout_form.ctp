<?php
echo $this->Form->create('CatalogItem', array('class' => 'form-catalogitem-layout text-right form-inline'));
echo $this->Form->input('layout', array(
	'label' => ' Layout: ',
	'div' => false, 
	'type' => 'select', 
	'default' => $catalogLayout['layout'],
	'style' => 'width: auto;',
));
echo $this->Form->input('per_page', array(
	'div' => false, 
	'type' => 'select', 
	'default' => $catalogLayout['per_page'],
	'label' => ' Per-Page: ',
	'style' => 'width: auto;',
));
echo $this->Form->button('Go', ['class' => 'btn btn-primary', 'type' => 'submit']);
echo $this->Form->end();
