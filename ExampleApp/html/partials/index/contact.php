<?php
$form_tools = $contact_form->get_bootstrap_form_tools('contact_form');
?>
<h1>Contact us</h1>
<div class="gpn_form">
    <p>Fields with <span class="required">*</span> are required.</p>

    <?= $form_tools->error_list() ?>
    <?= $form_tools->form_open(['action' => '/index/contact_submit', 'method' => 'post']) ?>
    <?= $form_tools->legend('Contact us') ?>

    <?= $form_tools->input_field_group('name', ['placeholder' => 'Enter name', 'required' => TRUE]) ?>
    <?= $form_tools->input_field_group('email', ['required' => TRUE]) ?>

    <?= $form_tools->input_field_group('telephone') ?>
    <?= $form_tools->input_field_group('address1') ?>
    <?= $form_tools->input_field_group('address2') ?>
    <?= $form_tools->input_field_group('address3') ?>
    <?= $form_tools->input_field_group('address4') ?>
    <?= $form_tools->input_field_group('subject', ['required' => TRUE]) ?>
    <?= $form_tools->textarea_field_group('message', ['required' => TRUE]) ?>

    <?= $form_tools->primary_submit('Submit') ?>
    <?= $form_tools->form_close() ?>
    <?= $contact_form->get_client_side_processor_code('contact_form') ?>
</div>
