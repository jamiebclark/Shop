<?php
$output = '';
if (empty($model)) {
	$model = 'OrderProduct';
}
if (!empty($productOptions)) {
	for ($i = 0; $i < 4; $i++) {
		$name = $model . '.product_option_choice_id_' . ($i + 1);
		
		if (empty($productOptions[$i])) {
			$output .= $this->Form->hidden($name, array('value' => null));
		} else {
			$optionLabel = $productOptions[$i]['ProductOption']['title'];
			$options = $productOptions[$i]['ProductOptionChoice'];
			
			if (!empty($admin)) {
				foreach ($options as $key => $values) {
					if (isset($values['disabled'])) {
						$options[$key]['disabled'] = false;
					}
				}
			}
			if (!empty($blank)) {
				$options = array('' => ' -- Select a ' . $optionLabel . ' --') + $options;
			}
			if (isset($label) && $label === false) {
				$optionLabel = false;
			}
			
			$output .= $this->Form->input($name, array(
				'options' => $options,
				'label' => $optionLabel,
			));
		}
	}
	if (!empty($legend)) {
		$output = $this->Layout->fieldset('Options', $output);
	}
}
echo $output;
?>