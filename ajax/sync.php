<?php
	require '../base.php';

	try {
		$items = $cloudApp->getItems(1, 64, CLOUD_API_TYPE_ALL, false);
		$trashed = $cloudApp->getItems(1, 64, CLOUD_API_TYPE_ALL, true);

		$items = array_merge($items, $trashed);
		$itemObjs = array();

		foreach ($items as $i) {
			$itemObjs[] = File::fromAPI($i);
		}

		File::deleteAll();
		File::insert($itemObjs);
	} catch (CloudApp\Exception $e) {
		echo 'Error: HTTP Code ' . $e->getHTTPCode();
	}
?>