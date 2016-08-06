<?php

$SUBJECT = 'Password reset request';
$FROM = 'ExampleApp Password reset <noreply@exampleapp.co.uk>';
$HTML_MESSAGE = '<h1>Your ExampleApp password has been reset.</h1>
<br/><br/>
Your username is: <strong>'.$username.'</strong> <br/>
Your new password is: <strong>'.$new_password.'</strong>
<br/><br/>
You can change this password after logging in to <a href="'.WWW_URL.'" title="Go to ExampleApp">ExampleApp.co.uk</a>
<br/><br/>
Kind regards,<br/>
<strong>The ExampleApp team</strong>
<br/><br/>
<small>If you did not request this password reset yourself, we recommend you contact us here: <a title="Email ExampleApp admin" href="'.htmlspecialchars(conf('app')['admin_email_address']).'">'.htmlspecialchars(conf('app')['admin_email_address']).'</a>.</small>
';

$TEXT_MESSAGE = strip_tags($HTML_MESSAGE);

include(__DIR__.'/template.php');
