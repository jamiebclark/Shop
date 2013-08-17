<?php
if (!empty($info) || (!isset($blank) || $blank !== false)) {
	if (empty($mode)) {
		$mode = 'infoTable';
	}
	$titleTag = 'h4';
	if ($mode == 'definitionList') {
		$output = $this->Layout->definitionList($info, array('class' => 'fullWidth'));
	} else {
		$output = $this->Layout->infoTable($info);
	}
	
	if (empty($tag)) {
		$tag = 'fieldset';
	}
	
	if ($tag == 'fieldset') {
		$titleTag = 'legend';
	}
	if (!empty($title)) {
		if (!empty($url)) {
			$title = $this->Html->link($title, $url);
		}
		$title = $this->Html->tag($titleTag, $title);
	} else {
		$title = '';
	}
	echo $this->Html->tag($tag, $title . $output);
}
