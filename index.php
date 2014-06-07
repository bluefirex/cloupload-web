<?php
	require 'base.php';

	$email = $db->getConfig('cloudapp.email');
	$pw = $db->getConfig('cloudapp.password');

	if (empty($email) || empty($pw)) {
		header("Location: ./setup.php#");
	}

	unset($email, $pw);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CloudApp | Local</title>
		<link rel="stylesheet" href="css/style.php" />

		<script src="./js/jquery.js"></script>
		<script type="text/javascript">
			var currentView = 'grid';

			function loadType(type) {
				var request = {
					type: type
				};

				$.ajax({
					url: 'ajax/loadType.php',
					type: 'GET',
					data: request
				}).done(function(a) {
					$('#content').html(a);
					$('#content .items').addClass(currentView);
				});
			}

			function parseParameters(fullURL) {
				var path = fullURL.replace(/(https?:\/\/)\S+#\//, '');
				var parameterString = path.split('?')[1];
				var singleParameterStrings = parameterString.split('&');
				var parameters = {};

				for (var i = 0; i < singleParameterStrings.length; i++) {
					var split = singleParameterStrings[i].split('=');
					parameters[split[0]] = split[1];
				}

				return parameters;
			}

			function openSettings() {
				$.ajax({
					url: 'ajax/settings.php',
					type: 'GET',
					data: {
						mode: 'page'
					}
				}).done(function(a) {
					$('#content').html(a);

					$('.actions .settings').addClass('active');
				});
			}

			function search(query) {
				$.ajax({
					url: 'ajax/search.php',
					type: 'GET',
					data: {
						query: query
					}
				}).done(function(a) {
					$('#content').html(a);
					$('#content .items').addClass(currentView);
				});
			}

			function sync() {
				var request = {
					sync: true
				};

				showOverlay('Syncing…');

				$.ajax({
					url: 'ajax/sync.php',
					type: 'POST',
					data: request
				}).done(function(a) {
					console.info('Sync response: ' + a);
					hideOverlay();

					if (a.substring(0, 5) == 'Error') {
						showTimedOverlay(a);
					} else {
						showTimedOverlay('Sync was successful.');
					}
					
					window.location.hash = '#/';
				});
			}

			function showTimedOverlay(txt) {
				showOverlay(txt);

				setTimeout(function() {
					hideOverlay();
				}, 3000);
			}

			function showOverlay(txt) {
				$('#overlay h1').html(txt);
				$('#overlay').addClass('shown').show(0);
			}

			function hideOverlay() {
				$('#overlay').removeClass('shown').delay(500).hide(0);
			}

			function switchView(newView) {
				switch (newView) {
					case 'grid':
						$('#content .items').removeClass('list').addClass('grid');
						$('header .actions ul.views li a').removeClass('active');
						$('header .actions ul.views li a[data-view=grid]').addClass('active');
						currentView = newView;
						break;

					case 'list':
						$('#content .items').removeClass('grid').addClass('list');
						$('header .actions ul.views li a').removeClass('active');
						$('header .actions ul.views li a[data-view=list]').addClass('active');
						currentView = newView;
						break;

					default:
						// Did you say something? o.o
				}
			}

			$(document).ready(function() {
				var windowHeight = $(window).height();

				$(window).resize(function() {
					windowHeight = $(window).height();
				});

				$(window).scroll(function() {
					if (windowHeight > $('nav').height() && $(window).scrollTop() > 63) {
						$('nav').addClass('floating');
					} else {
						$('nav').removeClass('floating');
					}
				});

				$(window).bind('hashchange', function() {
					var hash = window.location.hash.replace(/#/, '');
					var keyword = hash.replace(/\/([a-z]+)(\/(\S+))?/g, '$1').replace(/\?\S+/, '');

					$('nav ul li a, .actions .settings, header #searchquery').removeClass('active');

					switch (keyword) {
						case 'images':
						case 'bookmarks':
						case 'text':
						case 'archives':
						case 'audio':
						case 'video':
						case 'other':
						case 'trash':
							loadType(keyword);
							$('nav a[data-type='+keyword+']').addClass('active');
							break;

						case 'search':
							var query = parseParameters(self.location.href)['query'];
							$('header #searchquery').val(decodeURI(query)).addClass('active');
							search(query);
							break;

						case 'sync':
							sync();
							break;

						case 'settings':
							openSettings();
							break;

						default:
							loadType('all');
							$('nav a[data-type=all]').addClass('active');
					}
				});

				$('header #searchquery').keyup(function(e) {
					if (e.keyCode == 13) { // ENTER
						self.location.hash = '/search?query=' + encodeURI($(this).val());
					}
				});

				$(window).trigger('hashchange');
			});
		</script>
	</head>

	<body>
		<div id="overlay"><h1>{text}</h1></div>

		<header>
			<div class="left">
				<?=file_get_contents('images/cloudapp.svg'); ?>
				<span>Cloud</span>App
			</div>

			<div class="actions">
				<div class="fLeft" style="width: 49%;">
					<ul class="views">
						<li><a href="javascript:switchView('grid');" class="active" data-view="grid"><?=getSVG('grid'); ?></a></li>
						<li><a href="javascript:switchView('list');" data-view="list"><?=getSVG('details'); ?></a></li>
					</ul>
				</div>

				<div class="fRight" style="width:49%; text-align:right;">
					<ul>
						<li><a href="#/sync" title="Synchronize"><?=getSVG('sync'); ?></a></li>
						<li><a href="#/settings" class="settings" title="Settings"><?=getSVG('settings'); ?></a></li>
					</ul>

					&nbsp; &nbsp;
				</div>

				<div class="clear"></div>
			</div>

			<div class="search">
				<input type="text" name="query" id="searchquery" placeholder="Search…" />
			</div>
		</header>

		<div id="wrapper">
			<div id="sidebar">
				<nav>
					<ul>
						<li class="header">FILETYPES</li>
						<li><a href="#/" class="active" data-type="all"><?=getSVG('all'); ?> Everything</a></li>
						<li><a href="#/images" data-type="images"><?=getSVG('image'); ?> Images</a></li>
						<li><a href="#/bookmarks" data-type="bookmarks"><?=getSVG('bookmark'); ?> Bookmarks</a></li>
						<li><a href="#/text" data-type="text"><?=getSVG('text'); ?> Text</a></li>
						<li><a href="#/archives" data-type="archives"><?=getSVG('archive'); ?> Archives</a></li>
						<li><a href="#/audio" data-type="audio"><?=getSVG('audio'); ?> Audio</a></li>
						<li><a href="#/video" data-type="video"><?=getSVG('video'); ?> Video</a></li>
						<li><a href="#/other" data-type="other"><?=getSVG('unknown'); ?> Other</a></li>

						<li class="header">OTHER</li>
						<li><a href="#/trash" data-type="trash">Trash</a></li>
					</ul>
				</nav>

				<div class="upload">
					Click to<br />upload a file
				</div>
			</div>

			<div id="content">
				Loading...
			</div>
		</div>
	</body>
</html>