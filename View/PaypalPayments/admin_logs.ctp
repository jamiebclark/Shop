<style type="text/css">
.log-list {
	max-height: 400px;
	overflow: auto;
	overflow-x: hidden;
}
</style>
<h2>PayPal Payment Log</h2>
<h3><?php echo $logFile; ?></h3>
<div class="row">
	<div class="span9">
		<pre><?php echo $logFileContent;?></pre>
	</div>
	<div class="span3">
		<div class="log-list">
			<ul class="nav nav-list">
			<?php foreach ($logFiles as $file): ?>
				<li><?php echo $this->Html->link($file, array(0 => $file)); ?></li>
			<?php endforeach; ?>
			</ul>	
		</div>
	</div>
</div>