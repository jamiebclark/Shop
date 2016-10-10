<?php 
echo $this->Form->create();
echo $this->Form->hidden('id');
echo $this->Form->hidden('catalog_item_id');
?>
<fieldset>
	<legend>About</legend>
	<div class="row">
		<div class="col-sm-9">
			<?php echo $this->Form->input('title', [
				'label' => 'Name',
				'default' => 'Size',
				'after' => '<span class="help-block">Give a name to this grouping of options</span>'
			]); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->Form->input('index', [
				'type' => 'number', 
				'label' => 'Numerical Order',
				'after' => '<span class="help-block">If multiple options, decide their order</span>',
			]); ?>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>Choices</legend>
	<span class="help-block">List all of the different options available</span>
	<?php
	echo $this->element('Layout.form/element_input_list', [
		'function' => function($count) {
			$prefix = "ProductOptionChoice.$count";
			$out = $this->Form->hidden("$prefix.id", ['class' => 'element-input-list-key']);
			$out .= $this->Form->input("$prefix.title", ['label' => false, 'placeholder' => 'Options Choice']);
			return $out;
		},
		'model' => 'ProductOptionChoice',
	]);
	?>
</fieldset>
<?php
echo $this->Form->button('Save', ['class' => 'btn btn-primary']);
echo $this->Form->end();