<?php
	require 'base.php';

	if (isset($_POST['submit'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];

		try {
			$cloudApp = new CloudApp\API($email, $password, $db->getConfig('cloudapp.agent'));
			$accInfo = $cloudApp->getAccountInfo();

			if ($accInfo['email'] == $email) { // login successful!
				$db->setConfig('cloudapp.email', $email);
				$db->setConfig('cloudapp.password', $password);

				header("Location: ./#/sync");
			} else {
				throw new CloudApp\Exception('dafuq?', CLOUD_EXCEPTION_INVALID_RESPONSE);
			}
		} catch (CloudApp\Exception $e) { // not successful :'(
			$error = 'E-Mail or password are incorrect.';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CloudApp | Local | Setup</title>
		<link rel="stylesheet" href="css/style.php" />
		<script src="./js/jquery.js"></script>
		<script type="text/javascript">

		</script>

		<style>
			body {
				background: #fbfbfb;
			}
		</style>
	</head>

	<body>
		<div id="login">
			<header>
				<?=file_get_contents('images/cloudapp.svg'); ?>
				<span>Cloud</span>App
			</header>

			<?php if ($error): ?>
				<div class="error"><?php echo $error; ?></div>
			<?php endif; ?>

			<form method="post">
				<input type="text" name="email" value="" placeholder="E-Mail" class="email" />
				<input type="password" name="password" value="" placeholder="Password" class="password" />

				<input type="submit" name="submit" value="Login" />
			</form>

			<footer>
				<a href="https://github.com/bluefirex/cloupload-web" target="_blank">GitHub</a> &minus;
				<a href="http://cloupload.gidix.net" target="_blank">Cloupload for Android</a> &minus;
				<a href="http://bluefirex.com" target="_blank">by bluefirex</a><br /><br />

				<small>This inofficial client is not affiliated or endorsed by CloudApp in any way.<br />
					All rights belong to their respectful owners.</small>
			</footer>
		</div>
	</body>
</html>