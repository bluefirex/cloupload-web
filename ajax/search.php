<?php
	require '../base.php';

	$query = urldecode($_REQUEST['query']);
	
	echo '
		<h1>Search for "'.htmlspecialchars($query).'"</h1>

		<div class="items">
	';

	$items = File::getForQuery($query);

	foreach ($items as $item) {
		echo $item;
	}

	if (count($items) < 1) {
		echo '
			<div class="nothing"><h3>Sorry, no results.</h3></div>
		';
	}

	echo '
		</div>
	';
?>