<?php if (!empty($info) || (!isset($blank) || $blank !== false)):
	if (empty($mode)) {
		$mode = 'infoTable';
	}

	if ($mode == 'definitionList') {
		$output = $this->Html->div('panel-body', $this->Layout->definitionList($info));
	} else {
		$output = $this->Layout->infoTable($info);
	}
	
	if (!empty($title)) {
		if (!empty($url)) {
			$title = $this->Html->link($title, $url);
		}
	} else {
		$title = '';
	}
	?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="panel-title"><?php echo $title; ?></span>
		</div>
		<?php echo $output; ?>
	</div>
<?php endif;
