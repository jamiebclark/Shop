<h1>Shipping Rules</h1>
<?php
echo $this->Form->create();
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
		array('IF:', '&nbsp;'),
		array($this->Form->input(
			$prefix . 'min_quantity',
			array('label' => false)
		), 'Min. Quantity'),
		array($this->Form->input(
			$prefix . 'max_quantity',
			array('label' => false)
		), 'Max Quantity'),
		array('THEN:', '&nbsp;'),
		array($this->Form->input(
			$prefix . 'amt',
			array('label' => '$', 'style' => 'width:40px')
		), 'Amount Added'),
		array($this->Form->input(
			$prefix . 'per_item',
			array('label' => '$', 'style' => 'width:40px')
		), 'Amount Added Per Item'),
		array($this->Form->input(
			$prefix . 'pct',
			array('label' => false, 'after' => '%', 'style' => 'width:40px')
		), 'Percent Added of Sub-Total')
	), true);
}
echo $this->Table->table();
echo $this->FormLayout->submit('Update');
echo $this->Form->end();
