<?php
echo \WorkFrame\Html\Bootstrap_form_tools::alert('success', 'All done!', 'Thanks for registering with us.');
?>
<p>Before you can have access to the system, two things needs to happen:</p>
<ol>
	<li>You will need to confirm your email address. You should (very shortly) receive an email containing a confirmation link.</li>
	<li>To protect our service, we also need to validate your details. This should happen quite quickly (usually within one day.)</li>
</ol>
<p>You will receive an activation email once we verify your information.</p>
<p>Once you've received this you will be able to login (using the form at the top of the page.)</p>

<hr/>

<p>For further assistance, you may wish to contact us for help at this address <a title="Contact admin" href="mailto:<?=htmlspecialchars(conf('app')['admin_email_address'])?>"><?=htmlspecialchars(conf('app')['admin_email_address'])?></a>.</p>