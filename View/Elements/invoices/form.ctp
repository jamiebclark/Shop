<?php
echo $this->Form->create('Invoice');
echo $this->Form->hidden('id');
$span = 6;
?>
<div class="row">
	<div class="col-sm-4">
		<h3>Customer Info</h3>
		<?php
		echo $this->FormLayout->inputRow(array('first_name', 'last_name'));
		echo $this->AddressBookForm->inputAddress('Invoice');
		echo $this->FormLayout->inputRow(array('email', 'phone'));
		?>
	</div>
	<div class="col-sm-6">
		<h3>Payment Info</h3>
		<?php
		echo $this->Form->input('amt', array(
			'label' => 'Amount',
			'prepend' => '$',
			'placeholder' => '0.00',
			'class' => 'input-small',
			'step' => 'any',
		));
		echo $this->Form->input('recur', array(
			'label' => 'Recurring',
			'append' => 'per month',
			'class' => 'input-mini',
			'helpBlock' => 'Does payment repeat? (0 for no)',
		));
		echo $this->FormLayout->inputDatetime('paid', array(
			'label' => 'Date Paid',
			'control' => array('today', 'clear'),
			'blank' => true
		));
		echo $this->Form->input('invoice_payment_method_id', array('label' => 'Payment Method'));
		?>
	</div>
</div>
<?php echo $this->FormLayout->submitPrimary('Update'); ?>

<fieldset class="form-horizontal"><legend>Admin Settings</legend>
	<p class="note">How the Invoice is connected to other models in the database</p>
	<?php
	echo $this->FormLayout->inputs(array(
		'model' => array(
			'label' => 'Model Name',
			'helpInline' => 'The name of the model linked to the Invoice',
		),
		'model_id' => array(
			'type' => 'id', 
			'label' => 'Model ID',
			'helpInline' => 'The ID number linking the Invoice to the model in the system',
		),
		'model_title' => array(
			'label' => 'Model Display',
			'helpInline' => 'How the model will be displayed when it\'s displayed in the Invoice',
		),
		'user_id' => array(
			'type' => 'id', 
			'label' => 'User ID',
			'helpInline' => 'The User ID of the person who created the Invoice',
		)
	));
?></fieldset>
<?php echo $this->Form->end(); ?>