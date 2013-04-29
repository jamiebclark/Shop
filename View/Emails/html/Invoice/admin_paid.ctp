<?php
$url = array(
	'controller' => 'invoices', 
	'action' => 'view', 
	$invoice['Invoice']['id'],
	'admin' => true,
	'plugin' => 'shop',
);
?>
<h1><?php echo $this->Html->link("Invoice #{$invoice['Invoice']['id']} Paid", $url);?></h1>
<p>The following Invoice has been updated to <em>PAID</em>:</p>
<?php echo $this->element('invoices/info'); ?>
