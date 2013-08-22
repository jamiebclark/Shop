<style type="text/css">
.log-list {
	max-height: 400px;
	overflow: auto;
	overflow-x: hidden;
}
</style>
<h2>PayPal Payment Log</h2>
<?php echo $this->Html->link('Test', array('action' => 'test')); ?>
<h3><?php echo $logFile; ?></h3>
<div class="row">
	<div class="span9"><?php
		if (preg_match_all('/Tran[s]{0,1}action ID: ([A-Z0-9]+)/m', $logFileContent, $matches)) {
			foreach ($matches[0] as $k => $match) {
				$link = $this->Html->link('Transaction: ' . $matches[1][$k], array('action' => 'test', $matches[1][$k]));
				$logFileContent = str_replace($match, $link, $logFileContent);
			}
		}
		if (preg_match_all('/[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}-[\d]{2}:[\d]{2} /m', $logFileContent, $matches)) {
			foreach ($matches[0] as $match) {
				$logFileContent = str_replace($match, '<strong>'.date('F j, Y g:ia', strtotime($match)).':</strong> ', $logFileContent);
			}
		}
	?>
		<pre><?php echo $logFileContent;?></pre>
	</div>
	<div class="span3">
		<div class="log-list">
			<ul class="nav nav-list">
			<?php foreach ($logFiles as $file):
				echo $this->Html->tag('li',
					$this->Html->link($file, array(0 => $file)),
					array('class' => $file == $logFile ? 'active' : null)
				);
			endforeach; ?>
			</ul>	
		</div>
	</div>
</div>