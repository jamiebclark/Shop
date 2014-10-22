<?php
$prefix = "CatalogItemPackageChild.$count";
echo $this->Form->hidden("$prefix.id");
echo $this->Form->input("$prefix.catalog_item_child_id", array(
	'options' => $packageChildren,
	'label' => false,
	'after' => $this->Form->input("$prefix.quantity", array(
		'div' => false,
		'default' => 1,
		'beforeInput' => '<div class="input-group">',
		'afterInput' => '<span class="input-group-addon">Qty.</span></div>',
		'label' => false,
		'type' => 'number',
	))
));
