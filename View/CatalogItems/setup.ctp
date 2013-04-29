<h1>Setup Error</h1>
<p>The Shop Plugin could not be initialized, please see the errors below:</p>
<div class="errors">
<?php if (!empty($noBootstrap)): ?>
	<div>
		<h3>No Shop Bootstrap Loaded</h3>
		The bootstrap file was not loaded. Please make sure you have the variables set in 
		<code>app/Plugin/Shop/Config/bootstrap.php</code>. <br/>
		Also, make sure to load the bootstrap file in your app's <code>app/Config/bootstrap.php</code>:
		<pre><code>
		 CakePlugin::loadAll(array(
			'Shop' => array('bootstrap' => true)
		));
		</code></pre>
	</div>
<?php endif; ?>
</div>