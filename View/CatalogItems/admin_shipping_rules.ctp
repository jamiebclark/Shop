<style type="text/css">

</style>
<h1>Shipping Rules</h1>
<?php
debug($this->request->data);
echo $this->Form->create('CatalogItem');
echo $this->Form->hidden('CatalogItem.id', array('value' => $this->request->data['CatalogItem']['id']));

//echo $this->element('shipping_rules/inputs');
echo $this->FormLayout->inputList('shipping_rules/input', array('model' => 'ShippingRule'));

echo $this->Form->submit('Update');
echo $this->Form->end();
