<?php

namespace ExampleApp\Services;

class Contact_service extends \WorkFrame\Service {

	function new_form() {
		$do = $this->DOMAIN_OBJECT('Contact_form', 'Contact_form');
		return $do;
	}

	function submit($data) {
		/* @var $do \ExampleApp\Domain_objects\Contact_form */
		$do = $this->DOMAIN_OBJECT('Contact_form', 'Contact_form');
		$do->from_assoc($data);

		if ($do->process()) {
			$do->mark_submitted();

			$mailer = new \WorkFrame\Libraries\Mailer();
			$mailer->mail_from_template(conf('app')['admin_email_address'], 'contact_form.php', $do->to_assoc($do->all_fields()));
		}
		return $do;
	}

}
