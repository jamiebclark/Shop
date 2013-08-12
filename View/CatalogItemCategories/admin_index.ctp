<?php
echo $this->Layout->defaultHeader();
echo $this->CollapseList->output($catalogItemCategories, array(
	'actionMenu' => array(array('view', 'edit', 'delete', 'add'))
));
