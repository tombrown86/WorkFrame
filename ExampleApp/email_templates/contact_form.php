<?php

$SUBJECT = 'Contact form submitted';
$FROM = 'ExampleApp Contact form <noreply@exampleapp.co.uk>';
$HTML_MESSAGE = '<h1>Someone just submitted the Contact Us form.</h1>
<br/><br/>
<p><strong>Name:</strong> '.htmlspecialchars($name).'</p>
<p><strong>Telephone:</strong> '.htmlspecialchars($telephone).'</p>
<p><strong>Address:</strong> '.htmlspecialchars($address1).'</p>
<p><strong></strong> '.htmlspecialchars($address2).'</p>
<p><strong></strong> '.htmlspecialchars($address3).'</p>
<p><strong></strong> '.htmlspecialchars($address4).'</p>
<p><strong>Subject:</strong> '.htmlspecialchars($subject).'</p>
<p><strong>Message:</strong> '.htmlspecialchars($message).'</p>
';

$TEXT_MESSAGE = strip_tags($HTML_MESSAGE);

include(__DIR__.'/template.php');
