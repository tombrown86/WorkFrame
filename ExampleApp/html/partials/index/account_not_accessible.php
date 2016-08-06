<h1>Sorry - we can't let you in yet</h1>

<p>Before you can have access to the system, two things needs to happen:</p>
<ol>
	<li>You will need to confirm your email address. You should have been sent an email containing a confirmation link.</li>
	<li>To protect our service, we also need to validate your details. This should happen quite quickly (usually within one day.)</li>
</ol>
<p>You will receive an activation email once we verify your information.</p>
<p>Once you've received this, you will be able to login (using the form at the top of the page.)</p>
<br/>

<form action='/login/resend_confirm_email' method='post'>
	<legend>If you've not received a confirmation email:</legend>
	<input type='email' name='email' placeholder='Enter your email address'><button type='submit'>Send me a link to confirm my email address</button>
</form>

<hr/>

<h2>Shouldn't be seeing this?</h2>
<p>If you think you should have access or were unable to complete the above, you may wish to contact us for help at this address <a title="Contact admin" href="mailto:<?=htmlspecialchars(conf('app')['admin_email_address'])?>"><?=htmlspecialchars(conf('app')['admin_email_address'])?></a>.</p>