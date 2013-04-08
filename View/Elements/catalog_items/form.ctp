<?php 
echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', array('class' => 'form-horizontal', 'type' => 'file'));
echo $this->Form->inputs(array(
	'legend' => 'Item Info',
	'id',
	'title',
	'short_description',
	'description' => array('escape' => false),
	'price' => array('label' => 'Price', 'prepend' => '$'),
	'sale' => array('label' => 'Sale Price', 'prepend' => '$'),
	'min_quantity' => array('label' => 'Minimum Quantity'),
	'quantity_per_pack',
	'active' => array(
		'label' => 'Active',
		'helpBlock' => 'Whether item is available for sale',
	),
	'hidden' => array(
		'label' => 'Hidden',
		'helpBlock' => 'Hide this from catalog page while still being active',
	),
	'unlimited' => array(
		'label' => 'Unlimited Inventory',
		'helpBlock' => 'Item\'s stock never runs out'
	),
));
?>
<fieldset><legend>Categories</legend>
	<?php
		$total = 3;
		if (!empty($this->request->data['CatalogItemCategory'])) {
			$total += count($this->request->data['CatalogItemCategory']);
		}
		for ($i = 0; $i < $total; $i++) {
			echo $this->Form->input('CatalogItemCategory.' .$i, array(
				'type' => 'select',
				'label' => 'Category #' . ($i+1),
				'options' => $catalogItemCategories,
			));
		}
	?>
</fieldset>
<fieldset><legend>Images</legend>
	<?php
	$total = 1;
	if (!empty($this->request->data['CatalogItemImage'])) {
		$total += count($this->request->data['CatalogItemImage']);
	}
	for ($i = 0; $i < $total; $i++) {
		$prefix = 'CatalogItemImage.' . $i . '.';
		echo $this->Form->inputs(array(
			'fieldset' => false,
			$prefix . 'id' => array('type' => 'hidden'),
			$prefix . 'add_file' => array('type' => 'file'),
		));
	}
	?>
</fieldset>
<?php 
echo $this->Form->submit('Update');
echo $this->Form->end(); 