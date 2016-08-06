<?php

$SUBJECT = 'Account activated';
$FROM = 'ExampleApp activation request <noreply@exampleapp.co.uk>';
$HTML_MESSAGE = '<h1>Congratulations</h1>
<br/><br/>
<h2>Your account has been activated by our staff.</h2>
<br/>';

if($user->confirmed_email) {
	$HTML_MESSAGE .= '<p>You may now access your exampleapp account!</p>';
} else {
	$HTML_MESSAGE .= '<p>Once you have activated your email address, you will be able to access you account.</p>';
}

$HTML_MESSAGE .= '
<br/>
<p>Some details</p>
<p><strong>Your username</strong>: '.$user->get_username().'</p>
<p><strong>Website URL</strong>: <a title="ExampleApp website" href="'.WWW_URL.'"></a></p>
<p><strong>Admin email address</strong>: <a href="mailto:'.htmlspecialchars(conf('app')['admin_email_address']).'" title="Admin email address">'.htmlspecialchars(conf('app')['admin_email_address']).'</a></p>
<br/>
';

$HTML_MESSAGE .= '
Kind regards,<br/>
The ExampleApp team
';

$TEXT_MESSAGE = strip_tags($HTML_MESSAGE);

include(__DIR__.'/template.php');
