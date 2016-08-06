<!DOCTYPE html>
<html lang="en-US">
	<head>
		<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<?= script_tags($this->scripts) ?>
		<?= stylesheet_tags($this->stylesheets) ?>
		<!--[if IE 7]><link rel="stylesheet" href="/public/fontello/css/fontello-ie7.css"><![endif]-->
		
		<title><?= h($PAGE_TITLE) ?></title>
	</head>
	<body>
		<?= $HTML ?>
	</body>
</html>

