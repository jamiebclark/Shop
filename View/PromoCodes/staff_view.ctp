<?php
echo $this->Layout->defaultHeader($promoCode['PromoCode']['id']);
echo $this->Layout->infoResultTable($promoCode['PromoCode'], array(
	'title',
	'code',
	'pct' => array('format' => 'percent'),
	'amt' => array('format' => 'cash'),
	'started' => array('format' => 'date', 'label' => 'Starts'),
	'stopped' => array('format' => 'date', 'label' => 'Ends',),
	'active' => array('format' => 'yesno'),
));