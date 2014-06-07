<?php
	require '../lib/scss/compiler.php';
	echo compileSCSS('scss_cache/', $_GET['f'], true);
?>