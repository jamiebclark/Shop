<h2>PayPal Payment Log</h2>
<div class="row">
	<div class="span9">
		<pre><?php echo $logFileContent;?></pre>
	</div>
	<div class="span3">
		<ul class="nav nav-list">
		<?php foreach ($logFiles as $file): ?>
			<li><?php echo $this->Html->link($file, array(0 => $file)); ?></li>
		<?php endforeach; ?>
		</ul>	
	</div>
</div>