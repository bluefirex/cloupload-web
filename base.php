<?php
	error_reporting(E_ALL & ~E_NOTICE);
	use CloudApp\API as CloudApp;

	define('PATH', dirname(__FILE__) . '/');
	define('LIB', PATH . 'lib/');

	require LIB . 'Database.php';
	require_once LIB . 'CloudApp/API.php';
	require_once LIB . 'CloudApp/Exception.php';
	require LIB . 'PHPDateTime.php';
	require LIB . 'File.php';

	$db = Database::SQLite(PATH . '_db.sqlite3');

	$db->setConfig('app.secret', 'DONLTLOOKATME:(');
	$db->setConfig('cloudapp.agent', 'Cloupload Web/1.0 (PHP)');

	$cloudApp = new CloudApp($db->getConfig('cloudapp.email'), $db->getConfig('cloudapp.password'), $db->getConfig('cloudapp.agent'));

	function getSVG($path) {
		return file_get_contents(PATH . 'images/icons/' . $path . '.svg');
	}
?>