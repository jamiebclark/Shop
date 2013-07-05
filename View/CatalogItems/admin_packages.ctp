<?php
echo $this->element('products/admin_heading', array(
	'crumbs' => array(
		array($this->request->data['Product']['title'], array('action' => 'view', $this->request->data['Product']['id'])),
		'Packages'
	),
));

echo $this->Html->tag('h1', 'Product Packages');
echo $this->Html->tag('p', 'If this product is a grouping of other products in the store, list them below');
echo $this->Html->div('orderProductsForm');
echo $this->Form->create();
echo $this->Form->hidden('Product.id', array('value' => $this->request->data['Product']['id']));

$min = 5;
$buffer = 2;

$max = $buffer;
if (!empty($this->request->data['ProductPackageChild'])) {
	$max += count($this->request->data['ProductPackageChild']);
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