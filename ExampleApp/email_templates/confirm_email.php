<?php

$SUBJECT = 'Confirm your email address';
$FROM = 'ExampleApp Admin <'.conf('app')['admin_email_address'].'>';
$HTML_MESSAGE = '<h1>Thanks for registering</h1>
<br/><br/>
<p>This email address has just been registered against a ExampleApp '.$user->get_user_type().' '.$user->get_user_sub_type().' account.</p>
<br/><br/>
<p>Please confirm your identity by following this link to confirm your email address: <a href="'.  htmlspecialchars($confirm_url).'" title="Confirm email address"s>'.htmlspecialchars($confirm_url).'</a></p>
<br/>
<br/>
<p>If you just signed up, your account may not yet be accessible since we need a little time to verify new users.</p>
<br/>
<br/>
<p>If you weren\'t expecting this email, please ignore it.</p>
<br/>
<br/>
Kind regards,<br/>
The ExampleApp team
';

$TEXT_MESSAGE = strip_tags($HTML_MESSAGE);

include(__DIR__.'/template.php');
