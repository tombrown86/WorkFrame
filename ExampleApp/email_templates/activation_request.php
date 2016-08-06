<?php

$SUBJECT = 'Activation request';
$FROM = 'ExampleApp activation request <noreply@exampleapp.co.uk>';
$HTML_MESSAGE = '<h1>Someone just signed up.</h1>
<br/><br/>
<h2><a href="'. htmlspecialchars($activate_url).'" title="Activate user">Activate user</a></h2>
<br/>
'.  nl2br(htmlspecialchars(print_r($user->scenario_fields_to_assoc('sign_up'), 1))).'
<br/>
<h2><a href="'.  htmlspecialchars($activate_url).'" title="Activate user">Activate user</a></h2>
';

$TEXT_MESSAGE = strip_tags($HTML_MESSAGE);

include(__DIR__.'/template.php');
