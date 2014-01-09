<p><?php echo $this->Html->link('View Inventory Totals', array('controller' => 'products', 'action' => 'index')); ?></p>
<h2>Inventory History</h2>
<?php 
echo $this->Layout->defaultHeader();
echo $this->element('product_inventory_adjustments/table');