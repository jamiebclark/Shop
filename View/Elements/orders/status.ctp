<?php
if (!empty($info) || (!isset($blank) || $blank !== false)) {
	if (empty($mode)) {
		$mode = 'infoTable';
	}
	
	if ($mode == 'definitionList') {
		$output = $this->Layout->definitionList($info, array('class' => 'fullWidth'));
	} else {
		$output = $this->Layout->infoTable($info);
	}
	
	if (empty($tag)) {
		$tag = 'fieldset';
	}
	
	if ($tag == 'fieldset') {
		echo $this->Layout->fieldset($title, $output );
	} else {
		echo $this->Html->tag($tag);
		if (!empty($title)) {
			echo $this->Html->tag('h2', $title);
		}
		echo $output;
		echo "</$tag>\n";
	}
}
