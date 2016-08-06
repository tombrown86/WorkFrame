<?php
$form_tools = $user->get_bootstrap_form_tools('edit_profile');


?>
<h1>Edit profile</h1>

<div class="gpn_form">
	<?php if(@$update_success):?>
		<?=$form_tools->alert('success', 'Your profile has been saved')?>
	<?php endif; ?>
	<p>Fields with <span class="required">*</span> are required.</p>

	<?= $form_tools->error_list() ?>
	<?= $form_tools->form_open(['action' => '/profile/manage_submit', 'method' => 'post']) ?>

	<?php include(__DIR__ .'/../_global/profile_fields.php') ?>
	
	<input type="hidden" name="submitted" value="1"/>
	<?= $form_tools->primary_submit('Submit') ?>
	<?= $form_tools->form_close() ?>
	<?= $user->get_client_side_processor_code('edit_profile') ?>
</div>
