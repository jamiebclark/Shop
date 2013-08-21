<?php
class ShopSettingHelper extends AppHelper {
	var $helpers = array('Form', 'Layout.FormLayout', 'Html');
	
	function input($name, $options = array()) {
		$out = $this->Form->input("ShopSetting.$name.name", array(
			'value' => $name,
			'type' => 'hidden',
		));
		$out .= $this->FormLayout->input("ShopSetting.$name.value", $options + array('label' => $name));
		return $out;			
	}
	
	function inputs($inputs = array(), $options = array()) {
		$out = '';
		foreach ($inputs as $field => $fieldOptions) {
			if (is_numeric($field)) {
				$field = $fieldOptions;
				$fieldOptions = array();
			}
			$out .= $this->input($field, $fieldOptions);
		}
		if (!empty($options) && !is_array($options)) {
			$options = array('legend' => $options);
		}
		if (!empty($options['legend'])) {
			$legend = $this->Html->tag('legend', $options['legend']);
			if (!empty($options['note'])) {
				$legend .= $this->Html->tag('p', $options['note'], array('class' => 'note'));
			}
			$out = "<fieldset>$legend$out</fieldset>\n";
		}
		return $out;
	}
}