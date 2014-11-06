<h2>Product Packages</h2>
<p class="help-block">If this product is a grouping of other products in the store, list them below</p>
<?php
echo $this->Form->create();
echo $this->Form->hidden('CatalogItem.id');

$min = 5;
$buffer = 2;

$max = $buffer;
if (!empty($this->request->data['CatalogItemPackageChild'])) {
	$max += count($this->request->data['CatalogItemPackageChild']);
}
if ($max < $min) {
	$max = $min;
}

$this->Table->reset();
for ($k = 0; $k <= $max; $k++) {
	$prefix = 'ProductPackageChild.' . $k . '.';
	echo $this->Form->inputs(array(
		'fieldset' => false,
		$prefix . 'id' => array('type' => 'hidden'),
		$prefix . 'product_parent_id' => array('type' => 'hidden', 'value' => $this->request->data['Product']['id']),
	));
	
	$this->Table->cells(array(
		array($this->Form->input(
			$prefix . 'product_child_id',
			array('options' => $products, 'label' => false)
		)),
		array($this->Form->input(
			$prefix . 'quantity',
			array('type' => 'text', 'label' => false)
		), 'Quantity')
	), true);
}
echo $this->Table->output();
echo $this->FormLayout->submit('Update');
echo $this->Form->end();
echo "</div>\n";

?>