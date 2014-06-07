<?php
	require '../base.php';

	$mode = $_REQUEST['mode'];

	echo '<div style="text-align:center;margin-top:128px;margin-bottom:128px;"><h1>Coming soon!</h1></div>';

	echo '
		<div id="settings">
			<fieldset class="about">
				<legend>About</legend>

				<table width="100%" cellspacing="0" cellpadding="5" border="0">
					<tr>
						<td width="25%">
							<strong>Version:</strong>
						</td>

						<td>
							'.htmlspecialchars(file_get_contents('../ajax/VERSION')).'
						</td>
					</tr>

					<tr>
						<td>
							<strong>Created by:</strong>
						</td>

						<td>
							<a href="http://bluefirex.com" target="_blank">bluefirex</a>
						</td>
					</tr>
				</table>

			</fieldset>
		</div>
	';

	/*
	if ($mode == 'save') {

	} else {
		echo '
			<h1>Settings</h1>

			<br /><br />

			<div id="settings">
				<fieldset class="account">
					<legend>CloudApp Account</legend>
					
					<table border="0" width="100%" cellspacing="0" cellpadding="5">
						<tr>
							<td width="18%">
								E-Mail:
							</td>

							<td>
								<input name="email" type="email" value="'.$db->getConfig('cloudapp.email').'" style="width: 44%;" />
							</td>
						</tr>

						<tr>
							<td>
								Password:
							</td>

							<td>
								<input name="email" type="password" value="'.$db->getConfig('cloudapp.password').'" style="width: 44%;" />
							</td>
						</tr>

						<tr>
							<td>
								&nbsp;
							</td>

							<td>
								<input type="submit" name="save_account" value="Save" />
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		';
	}*/
?>