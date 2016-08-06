<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8"/>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<?= script_tags($this->scripts) ?>
		<?= stylesheet_tags($this->stylesheets) ?>

		<title><?= $PAGE_TITLE ?></title>
	</head>
	<body>

		<div class="wrap">
			<nav class="navbar-inverse navbar-fixed-top navbar" role="navigation">
				<div class="navbar-header">
					<a class="navbar-brand" href="/"><?= h($PAGE_TITLE) ?></a>
				</div>
				<div class="collapse navbar-collapse">
					<ul id="w264" class="navbar-nav nav">
						<li><a href="<?=WWW_ROOT_PATH?>/">A menu item</a></li>
						<li class="dropdown">
							<a aria-expanded="false" class="dropdown-toggle" href="#" data-toggle="dropdown">Drop down <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#" tabindex="-1">Sub Menu item 1</a></li>
								<li><a href="#" tabindex="-1">Sub Menu item 2</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>


			<div class="row">
				<div class="col-md-2">
					<div id="navigation" class="list-group">
						<a class="list-group-item" href="#navigation-1" data-toggle="collapse" data-parent="#navigation">Home <b class="caret"></b></a>
						<div id="navigation-1" class="submenu panel-collapse collapse">
							<a class="list-group-item" href="<?=WWW_ROOT_PATH?>/">About Framework</a>
							<a class="list-group-item" href="<?=WWW_ROOT_PATH?>/index/author">About author</a>
						</div>
						<a class="list-group-item" href="#navigation-2" data-toggle="collapse" data-parent="#navigation">Menu item 2 <b class="caret"></b></a>
						<div id="navigation-2" class="submenu panel-collapse collapse">
							<a class="list-group-item" href="#">Sub menu item 1</a>
							<a class="list-group-item" href="#">Sub menu item 2</a>
						</div>
					</div>
				</div>
				<div class="col-md-9" role="main" id="main_content">
					<?= $HTML ?>
				</div>
			</div>

		</div>

		<div class="toplink"><a href="#" class="h1" title="Back to top">^</div>

		<footer class="footer">
			<p class="pull-right"><small>Developed by <a href="http://tombrown.xyz">tombrown.xyz</a> - any 3rd party code will be credited inline</small></p>
			Powered by <a href="http://tombrown.xyz/workframe" rel="external">Workframe</a></footer>

		<script type="text/javascript">jQuery(document).ready(function () {
	//                var shiftWindow = function () {
	//                    scrollBy(0, -50)
	//                };
	//                if (location.hash)
	//                    setTimeout(shiftWindow, 1);
	//                window.addEventListener("hashchange", shiftWindow);
	//                var element = document.createElement("script");
	//                element.src = "./jssearch.index.js";
	//                document.body.appendChild(element);
	//
	//                var searchBox = $('#searchbox');


		});</script>
	</body>
</html>

