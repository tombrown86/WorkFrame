<h1>Hello <?=htmlspecialchars($user->get_username)?>,</h1>
<h2>Thanks for confirming your email address.</h2>
<?php if($user->activated): ?>
	<br/><h3>Your account is now fully active! You can login right away using the form at the top of the page.</h3>
<?php else: ?>
	<h3>Once we have verified your details, you will be able to access your account.</h3>
	<br/><br/>
	<h3>We hope to see you soon!</h3>
<?php endif; ?>
<br/>