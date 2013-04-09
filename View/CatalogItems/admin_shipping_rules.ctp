<style type="text/css">
td input {
	width: 80px;
	font-size: 2em;
}
td div {
	margin-bottom: 0 !important;
}
</style>
<h1>Shipping Rules</h1>
<?php
echo $this->Form->create('CatalogItem');
echo $this->Form->hidden('CatalogItem.id', array('value' => $this->request->data['CatalogItem']['id']));

$min = 5;
$buffer = 2;

$max = $buffer;
if (!empty($this->request->data['ProductPackageProduct'])) {
	$max += count($this->request->data['ProductPackageProduct']);
}
if ($max < $min) {
	$max = $min;
}
$this->Table->reset();
for ($k = 0; $k <= $max; $k++) {
	$prefix = 'ShippingRule.' . $k . '.';
	echo $this->Form->inputs(array(
		'fieldset' => false,
		$prefix . 'id' => array('type' => 'hidden'),
		$prefix . 'catalog_item_id' => array('type' => 'hidden', 'value' => $this->request->data['CatalogItem']['id']),
	));
	
	$this->Table->cells(array(
		array('IF:', '&nbsp;', array('th' => true)),
		array($this->Form->input(
			$prefix . 'min_quantity',
			array('label' => false)
		), 'Min. Quantity'),
		array($this->Form->input(
			$prefix . 'max_quantity',
			array('label' => false)
		), 'Max Quantity'),
		array('THEN:', '&nbsp;', array('th' => true)),
		array($this->Form->input($prefix . 'amt', array(
				'label' => false,
				'prepend' => '$', 
				'class' => 'number')
		), 'Amount Added'),
		array($this->Form->input($prefix . 'per_item', array(
				'label' => false,
				'prepend' => '$', 
			)
		), 'Amount Added Per Item'),
		array($this->Form->input(
			$prefix . 'pct',
			array(
				'label' => false, 
				'append' => '%', 
			)
		), 'Percent Added of Sub-Total')
	), true);
}
echo $this->Table->output();
echo $this->FormLayout->submit('Update');
echo $this->Form->end();
