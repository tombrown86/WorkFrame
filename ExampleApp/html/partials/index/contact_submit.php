<h1>...sent !</h1>
<?php
$form_tools = $contact_form->get_bootstrap_form_tools('contact_form');
echo $form_tools->alert('success', 'Thanks for you message!', 'We usually respond very quickly.');
?>