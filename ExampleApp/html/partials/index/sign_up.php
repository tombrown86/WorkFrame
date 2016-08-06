<?php
$form_tools = $user->get_bootstrap_form_tools('sign_up_form');


?>
<h1>Sign up</h1>
<div class="gpn_form">
    <p>Fields with <span class="required">*</span> are required.</p>

	<?= $form_tools->error_list() ?>
	<?= $form_tools->form_open(['action' => '/index/sign_up_submit?type=user&subtype=editor', 'method' => 'post']) ?>
  
	<?php include(__DIR__ .'/../_global/profile_fields.php') ?>


	<?= $form_tools->legend('Finally') ?>
	<?php $how_hear_options = [''=>'', 'A colleague'=>'A colleague', 'A marketing email'=>'A marketing email', 'From your surgery/practice' => 'From your surgery/practice', 'other'=>'Other']; ?>
	<?= $form_tools->select_field_group('how_hear_about_us', ['options'=>$how_hear_options, 'required' => TRUE, 'attributes'=>['onchange'=>'if(this.value=="other") $("#how_hear_other").show(); else $("#how_hear_other").hide();']]) ?>
	<div id="how_hear_other" style="<?php if(in_array($user->get_how_hear_about_us(), $how_hear_options)) echo 'display:none'; ?>">
		<?= $form_tools->input_field_group('how_hear_about_us_other') ?>
	</div>

	<?= $form_tools->input_field('type', ['type'=>'hidden']) ?>
	<?= $form_tools->input_field('subtype', ['type'=>'hidden']) ?>
	<?= $form_tools->primary_submit('Submit') ?>
	<?= $form_tools->form_close() ?>
	<?= $user->get_client_side_processor_code('sign_up_form') ?>
</div>
