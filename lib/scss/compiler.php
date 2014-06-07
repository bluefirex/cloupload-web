<?php
	require_once 'scss.inc.php';

	function compileSCSS($base, $file, $outputHeader = false) {
		$scss = new scssc();
		$scss->setFormatter('scss_formatter_compressed');

		$file = basename($file);
		$info = pathinfo($file);

		if (!isset($info['extension']) || ($info['extension'] != 'scss' && $info['extension'] != 'css') || !is_file($file)) {
			$file = 'style.scss';
			$info = pathinfo($file);
		}
		
		$outfile = $base . $info['filename'] . '.compiled.css';
		$contents = '';

		if (@filemtime($outfile) < filemtime($file) || $_GET['chuck'] == 'norris') {
			try {
				file_put_contents($outfile, $scss->compile(file_get_contents($file)));
			} catch (Exception $e) {
				$contents .= "/*\nCOMPILATION ERROR:\n" . $e . "\n*/\n";
			}
		}

		if ($outputHeader) {
			header("Content-Type: text/css");
		}

		$contents .= file_get_contents($outfile);
		return $contents;
	}
?>