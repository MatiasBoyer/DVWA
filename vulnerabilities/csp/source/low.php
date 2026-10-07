<?php

header("Content-Security-Policy: script-src 'self'; object-src 'none'; base-uri 'self'");

if (isset($_POST['include'])) {
	$page['body'] .= '<p>External script includes are not allowed.</p>';
}

$page['body'] .= '
<form name="csp" method="POST">
	<p>Scripts can only be loaded from this site.</p>
	<input size="50" type="text" name="include" value="" id="include" />
	<input type="submit" value="Include" />
</form>
';

?>
