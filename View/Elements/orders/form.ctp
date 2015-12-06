<?php
echo $this->Form->create('Order');
echo $this->Form->hidden('id');
echo $this->Form->hidden('sub_total');
echo $this->Form->hidden('shipping');

$span = 6;

if (!empty($order['Order']['id'])) {
	echo $this->Layout->defaultHeader($order['Order']['id']);
}
?>

	<?php	
	echo $this->Form->input('canceled', [
		'label' => 'Canceled', 
		'class' => 'checkbox',
		'after' => '<span class="help-block">Order has been canceled</span>'
	]);
	?>
<div class="row">
	<div class="col-sm-5">
		<fieldset>
			<legend>Shipping</legend>
			<?php echo $this->element('orders/input_shipping'); ?>
		</fieldset>
	</div>
	<div class="col-sm-7">
		<fieldset>
			<legend>Customer Info</legend>
			<?php
			echo $this->FormLayout->addressInput(array(
				'placeholder' => true,
				'before' => $this->FormLayout->inputRow(['first_name', 'last_name']),
				'after' => $this->FormLayout->inputRow(['email', 'phone'], ['placeholder' => true]),
			));
			?>
		</fieldset>
	</div>
</div>

<div class="row">
	<div class="col-sm-5">
		<fieldset>
			<legend>Payment</legend>
			<?php echo $this->element('orders/input_payment', ['amt' => false]);?>
		</fieldset>
	</div>
	<div class="col-sm-7">
		<fieldset>
			<legend>Billing</legend>
			<?php 
			echo $this->Layout->toggle(null, $this->FormLayout->addressInput(compact('span') + array(
				'model' => 'Invoice',
				'placeholder' => true,
				'before' => $this->FormLayout->inputRow([
					'Invoice.first_name', 
					'Invoice.last_name'
				], compact('span'))
			)), 'Billing address is same as Shipping', [
				'name' => 'same_billing',
			]);
			?>
		</fieldset>
	</div>
</div>

<fieldset>
	<legend>Cart</legend>
	<?php
	echo $this->Form->inputs([
		'fieldset' => false,
		'auto_shipping' => [
			'type' => 'checkbox',
			'label' => 'Let the system calculate what shipping charges should be',
			'class' => 'checkbox',
		],
		'auto_price' => [
			'type' => 'checkbox',
			'label' => 'Let the system calculate product prices',
			'class' => 'checkbox',
		]
	]);
	$this->Table->reset();
	$inputOptions = [
		'label' => false, 
		'class' => 'input-small form-control'
	];
	$cashOptions = $inputOptions + [
		'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
		'afterInput' => '</div>', 
		'step' => 'any'
	];

	$View = $this;
	echo $this->FormLayout->inputList(function($count) use ($View) {
		$prefix = "OrderProduct.$count";
		$out = $View->Form->input("$prefix.id");
		$out .= $View->FormLayout->inputRow([
			"$prefix.product_id",
			"$prefix.price" => ['type' => 'cash'],
			"$prefix.shipping" => ['type' => 'cash'],
			"$prefix.quantity" => ['type' => 'number'],
		]);
		return $out;
	}, [
		'model' => 'OrderProduct',
		'addBlank' => 0,
	]);

	?>
</fieldset>

<fieldset>
	<legend>Handling</legend>
	<?php
	echo $this->Form->input('auto_handling', [
		'type' => 'checkbox',
		'label' => 'Let the system calculate what handling charges should be',
		'class' => 'checkbox',
	]);
	echo $this->Table->reset();
	$total = 1;
	if (!empty($this->request->data['OrdersHandlingMethod'])) {
		$total = count($this->request->data['OrdersHandlingMethod']);
	}
	for ($k = 0; $k <= $total; $k++) {
		$prefix = 'OrdersHandlingMethod.' . $k . '.';
		
		if (!empty($this->request->data['OrdersHandlingMethod'][$k])) {
			$total = ($this->request->data['Order']['sub_total'] + $this->request->data['Order']['shipping']);
			$total *= $this->request->data['OrdersHandlingMethod'][$k]['pct'];
			$total += $this->request->data['OrdersHandlingMethod'][$k]['amt'];
		} else {
			$total = 0;
		}
		echo $this->Form->hidden($prefix . 'id');
		echo $this->Form->hidden($prefix . 'handling_method_id');
		$this->Table->cells(array(
			array(
				$this->Form->input(
					$prefix . 'title', 
					['prepend' => false, 'class' => 'form-control'] + $inputOptions
				), 
				'Title',
				['class' => 'col-sm-4']
			), array(
				$this->Form->input($prefix . 'amt', $cashOptions), 
				'Amount',
				['class' => 'col-sm-2']
			), array(
				$this->Form->input($prefix . 'pct', $inputOptions + [
					'beforeInput' => '<div class="input-group">',
					'afterInput' => '<span class="input-group-addon">%</span></div>'
				]), 
				'Percent',
				['class' => 'col-sm-2']
			), array(
				$this->FormLayout->fakeInput($this->DisplayText->cash($total), ['label' => false]), 'Charge',
				['class' => 'price']
			),
		), true);
	}
	echo $this->Table->output(['class' => 'handling-methods']);
	?>
</fieldset>

<?php 
echo $this->Form->submit('Update', ['class' => 'btn btn-primary']); 
echo $this->Form->end();
$this->Asset->blockStart();
?>
(function($) {
	$.fn.toggleActive = function(find) {
		return this.each(function() {
			var $toggle = $(this);
			function toggleClick() {
				$(find).each(function() {
					if ($toggle.is(':checked')) {
						$(this).data('old-readonly', $(this).prop('readonly'));
						$(this).prop('readonly', true);
					} else {
						$(this).prop('readonly', false);
					}
				});
			}
			
			$toggle.click(function(e) {
				toggleClick();
			});
			toggleClick();
			return $(this);
		});
	};
})(jQuery);
$(document).ready(function() {
	$('input[name*=auto_shipping]').toggleActive('input[name*="[shipping]"]');
	$('input[name*=auto_price]').toggleActive('input[name*="[price]"]');
	$('input[name*=auto_handling]').toggleActive('.handling-methods :input');
});
<?php
$this->Asset->blockEnd();