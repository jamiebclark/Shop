<?php
echo $this->Form->create('Invoice');
echo $this->Form->hidden('id');
$span = 6;
?>
<div class="row">
	<div class="col-sm-8">
		<div class="row">
			<div class="col-sm-6">
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
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
					'afterInput' => '</div>',
					'placeholder' => '0.00',
					'step' => 'any',
					'after' => '<span class="help-block">Gross donation</span>',
				));
				echo $this->Form->input('net', array(
					'label' => 'Net Amount',
					'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
					'afterInput' => '</div>',
					'placeholder' => '0.00',
					'step' => 'any',
					'after' => '<span class="help-block">Leave blank to match the gross amount</span>',
				));
				echo $this->Form->input('recur', array(
					'label' => 'Recurring',
					'beforeInput' => '<div class="input-group">',
					'afterInput' => '<span class="input-group-addon">per month</span></div>',
					'after' => '<span class="help-block">Does payment repeat? (0 for no)</span>',
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
			<p class="help-block">How the Invoice is connected to other models in the database</p>
			<?php
			echo $this->FormLayout->inputs(array(
				'model' => array(
					'label' => 'Model Name',
					'after' => '<span class="help-block">The name of the model linked to the Invoice</span>',
				),
				'model_id' => array(
					'type' => 'id', 
					'label' => 'Model ID',
					'after' => '<span class="help-block">The ID number linking the Invoice to the model in the system</span>',
				),
				'model_title' => array(
					'label' => 'Model Display',
					'after' => '<span class="help-block">How the model will be displayed when it\'s displayed in the Invoice</span>',
				),
				'user_id' => array(
					'type' => 'id', 
					'label' => 'User ID',
					'after' => '<span class="help-block">The User ID of the person who created the Invoice</span>',
				)
			));
		?></fieldset>
	</div>
</div>
<?php echo $this->Form->end(); ?>