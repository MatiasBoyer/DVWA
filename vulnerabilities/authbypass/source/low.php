<?php

if (dvwaCurrentUser() !== 'admin') {
	http_response_code(403);
	echo 'Unauthorised';
	exit;
}

?>
