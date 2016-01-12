<?php 
echo $this->Form->create();
echo $this->Form->hidden('id');
echo $this->Form->hidden('catalog_item_id');
?>
<fieldset>
	<legend>About</legend>
	<div class="row">
		<div class="col-sm-9">
			<?php echo $this->Form->input('title', ['label' => 'Name']); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->Form->input('index', ['type' => 'number', 'label' => 'Numerical Order']); ?>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>Choices</legend>
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