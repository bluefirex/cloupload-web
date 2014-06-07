<?php
	require '../base.php';

	$type = $_GET['type'];

	switch ($type) {
		case 'images':
			$items = File::getItems(File::TYPE_IMAGE);
			break;

		case 'bookmarks':
			$items = File::getItems(File::TYPE_BOOKMARK);
			break;

		case 'text':
			$items = File::getItems(File::TYPE_TEXT);
			break;

		case 'archives':
			$items = File::getItems(File::TYPE_ARCHIVE);
			break;

		case 'audio':
			$items = File::getItems(File::TYPE_AUDIO);
			break;

		case 'video':
			$items = File::getItems(File::TYPE_VIDEO);
			break;

		case 'other':
			$items = File::getItems(File::TYPE_UNKNOWN);
			break;

		case 'trash':
			$items = File::getItems(File::TRASHED);
			break;

		default:
			$items = File::getItems(File::TYPE_ALL);
	}

	echo '<div class="items">';

	foreach ($items as $item) {
		echo $item;
	}

	if (count($items) < 1) {
		echo '
			<div class="nothing">
				<h3>You have no items here, yet.</h3>
				<span>Start uploading some!</span>
			</div>
		';
	}

	echo '</div>';
?>