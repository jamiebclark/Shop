<?php
if (empty($model)) {
	$model = 'OrderProduct';
}
$prefix = $model . '.';
if (isset($key)) {
	$prefix .= $key . '.';
}
echo $this->Html->div('productOptionSelect');
if (empty($productOptions)) {
	echo '&nbsp;';
} else {
	foreach ($productOptions as $productOption) {
		if (!empty($productOption['ProductOption'])) {
			$productOptionInfo = $productOption['ProductOption'];
		} else {
			$productOptionInfo = $productOption;
		}
		$options = array();
		if (!empty($productOption['ProductOptionChoice'])) {
			foreach ($productOption['ProductOptionChoice'] as $productOptionChoice) {
				//$options[$productOptionChoice['id']] = $productOptionChoice['title'];
				$options[] = $productOptionChoice;
			}
		}
		echo $this->Form->input(
			$prefix . 'product_option_choice_id_' . $productOptionInfo['index'],
			array(
				'options' => $options,
				'label' => $productOptionInfo['title'],
				'div' => false,
			)
		);
	}
}
echo "</div>";
