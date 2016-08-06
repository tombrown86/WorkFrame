
	<?= $form_tools->legend('Contact details') ?>

	<?= $form_tools->input_field_group('first_name', ['required' => TRUE]) ?>
	<?= $form_tools->input_field_group('last_name', ['required' => TRUE]) ?>
	
	<?= $form_tools->input_field_group('email', ['required' => TRUE, 'type'=>'email']) ?>
	<?= $form_tools->field_group('address1', 
			$form_tools->input_field('address1', ['required' => TRUE]) .
			$form_tools->input_field('address2', ['required' => TRUE]) .
			$form_tools->input_field('address3') . 
			$form_tools->input_field('address4')) ?>
	<?= $form_tools->input_field_group('postcode', ['required' => TRUE, 'width'=>'medium']) ?>
	<?= $form_tools->input_field_group('phone', ['required' => TRUE]) ?>
	<?= $form_tools->input_field_group('phone2', []) ?>
	<?= $form_tools->input_field_group('fax') ?>
	
	<?= $form_tools->legend('Account details') ?>
	
	<?php if($user->get_scenario() == 'sign_up'): ?>
		<?= $form_tools->input_field_group('username', ['required' => TRUE]) ?>
	<?php else:?>
		<?= $form_tools->input_field_group('username', ['required' => TRUE, 'attributes'=>['disabled'=>'disabled']]) ?>
	<?php endif;?>
	
	<?php if($user->get_scenario() != 'sign_up'): ?>
		<?= $form_tools->checkbox_field_group('change_password', ['attributes'=>['onclick'=>'if(this.checked) $("#change_password").show(); else  $("#change_password").hide();']]) ?>
	
		<div id="change_password" style="<?php if(!$user->get_change_password()) echo 'display:none';?>">
			<?= $form_tools->input_field_group('password', ['type'=>'password', 'placeholder'=>'New password']) ?>
			<?= $form_tools->input_field_group('password2', ['type'=>'password']) ?>
		</div>
	<?php else:?>
		<?= $form_tools->input_field_group('password', ['required' => TRUE, 'type'=>'password']) ?>
		<?= $form_tools->input_field_group('password2', ['required' => TRUE, 'type'=>'password']) ?>
	<?php endif;?>
	
	<?= $form_tools->legend('Profile message') ?>
	<?= $form_tools->textarea_field_group('message', ['placeholder'=>'Please provide a short message for your profile']) ?>
	