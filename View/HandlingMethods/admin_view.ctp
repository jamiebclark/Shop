<h1><?php echo $handlingMethod['HandlingMethod']['title']; ?></h1>
<?php
echo $this->Layout->headerMenu(array(
	array('Edit Handling Charge', array('action' => 'edit', $handlingMethod['HandlingMethod']['id'])),
	array('Delete Handling Charge', array('action' => 'delete', $handlingMethod['HandlingMethod']['id']), null, 'Delete this charge?')
));

echo $this->Layout->infoResultTable($handlingMethod['HandlingMethod'], array(
	'title',
	'pct' => array('format' => 'percent'),
	'amt' => array('format' => 'cash'),
	'active' => array('format' => 'yesno'),
));
?>