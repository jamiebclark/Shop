<?php
echo $this->Form->create('Invoice');
echo $this->Form->hidden('id');

echo $this->Layout->fieldset('Customer Info');
echo $this->element('profiles/full_name_input', array('model' => 'Invoice'));
echo $this->element('locations/form', array(
	'model' => 'Invoice',
	'location' => true,
	'addline' => 2,
	'cityStateLine' => true,
));
echo $this->Form->input('email');
echo $this->Form->input('phone');
echo $this->FormLayout->submit('Update');
echo "</fieldset>\n";

echo $this->Layout->fieldset('Payment Info');
echo $this->Form->input('amt', array('label' => 'Amount $'));
echo $this->Form->input('recur', array('label' => 'Recurring'));
echo $this->DateBuild->input('paid', array(
	'class' => 'datetime',
	'label' => 'Date Paid',
	'control' => array('today', 'clear'),
	'blank' => true
));
echo $this->Form->input('invoice_payment_method_id', array('label' => 'Payment Method'));
echo $this->FormLayout->submit('Update');
echo "</fieldset>\n";



echo $this->Layout->fieldset('Admin Settings');
echo $this->Form->input('user_id', array('type' => 'text', 'label' => 'User ID'));
echo "</fieldset>\n";

echo $this->Form->end();
?>