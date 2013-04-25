<?php
$min = 5;
$buffer = 2;

$max = $buffer;
if (!empty($this->request->data['ProductPackageProduct'])) {
	$max += count($this->request->data['ProductPackageProduct']);
}

if ($max < $min) {
	$max = $min;
}
?>
<div class="shipping-rules">
	<div class="row-fluid title">
		<div class="span6">If matches:</div>
		<div class="span6">Then add:</div>
	</div>
	<?php 
	for ($count = 0; $count <= $max; $count++) {
		echo $this->element('shipping_rules/input', compact('count'));
	}
	?>
</div>
