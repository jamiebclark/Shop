<?php
echo $this->Form->create();
echo $this->Form->inputs(array(
	'id',
	'title',
	'url' => array(
		'label' => 'Tracking URL',
		'helpBlock' => 'If this shipping method can be tracked online, include the URL. The tracking ID will be appended to the URL, so include query data like "http://example.com?tracking_id="',
		'class' => 'input-xxlarge',
	),
	'fieldset' => false,
));
echo $this->Form->submit('Update');
echo $this->Form->end();