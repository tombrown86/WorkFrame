<!DOCTYPE html>
<html lang="en-US">
	<head>
		<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<?= script_tags($this->scripts) ?>
		<?= stylesheet_tags($this->stylesheets) ?>
		<!--[if IE 7]><link rel="stylesheet" href="/public/fontello/css/fontello-ie7.css"><![endif]-->

		<title><?= h($PAGE_TITLE) ?></title>
	</head>
	<body>

		<div class="wrap">
			<nav class="navbar-inverse navbar-fixed-top navbar" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#topnavmenu"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
					<a class="navbar-brand" href="/"><?= h($PAGE_TITLE) ?></a>
				</div>


				<div id="topnavmenu" class="collapse navbar-collapse">
					<ul class="navbar-nav nav">
						<li><a href="<?= WWW_ROOT_PATH ?>/">Home</a></li>
						<li><a href="<?= WWW_ROOT_PATH ?>/index/sign_up">Sign up</a></li>
						<li><a href="<?=WWW_URL?>/newsandsupport/">Support</a></li>
						<li><a href="<?= WWW_ROOT_PATH ?>/index/contact">Contact</a></li>
					</ul>
					<?php if ($this->Current_user->is_authenticated()): ?>
						<form method="get" action="/login/logout" id="login" class="navbar-form navbar-right" role="form">
							<strong id="login_panel_welcome">Hello <?=h($this->Current_user->get_user()->get_username())?></strong>
							<button type="submit" class="btn btn-primary">Logout</button>
						</form>
					<?php else: ?>
						<form onsubmit="login_submit(this);return false;" id="login" class="navbar-form navbar-right" role="form">
							<div class="input-group">
								<span class="input-group-addon"><i class="fontello-user"></i></span>
								<input name="username" class="form-control" value="" placeholder="Enter your username">                                        
							</div>

							<div class="input-group">
								<span class="input-group-addon"><i class="fontello-key"></i></span>
								<input name="password" type="password" class="form-control" value="" placeholder="Password">                                        
							</div>

							<button type="submit" class="btn btn-primary">Login</button>
						</form>
					<?php endif; ?>
				</div>

			</nav>


			<div class="row">
				<div class="col-md-2">

					<div id="navigation" class="list-group">
						<?php if (!$this->Current_user->is_authenticated()): ?>
							<a class="list-group-item" href="<?= WWW_ROOT_PATH ?>/index/about" data-parent="#navigation">About</a>
							<a class="list-group-item" href="<?= WWW_ROOT_PATH ?>/index/learn_more" data-parent="#navigation">Learn more</a>
							<a class="list-group-item" href="#navigation-sub1" data-toggle="collapse" data-parent="#navigation">Sign up <b class="caret"></b></a>
							<a class="list-group-item" href="<?= WWW_ROOT_PATH ?>/index/sign_up">Sign up</a>

						<?php else: ?>

							<a class="list-group-item" href="<?= WWW_ROOT_PATH ?>/login/logout">Logout</a>
							
						<?php endif; ?>
					</div>
				</div>
				<div class="col-md-9" role="main" id="main_content">
					<?= $HTML ?>
				</div>
			</div>

		</div>

		<div class="toplink"><a href="#" class="h1" title="Back to top">^</div>

		<div class="modal fade" id="reset_password_modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">Reset password for email address</h4>
					</div>
					<div class="modal-body">
						<p><small>You will also received a username reminder.</small></p>
						<input onkeyup="validate_reset_password_form()" id="reset_password_email" type="email" placeholder="Email address"/>
					</div>
					<div class="modal-footer">
						<button id="reset_password_submit" onclick="reset_password_submit();return false" type="button" class="btn btn-primary">Reset</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->




		<footer class="footer">
			<p class="pull-right">
				<small>In partnership with <a title="SELF - Sussex Education and Learning Forum" href="http://www.self-online.co.uk">www.self-online.co.uk</a></small>
			</p>
			Copyright <?=date('Y')?></footer>

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

