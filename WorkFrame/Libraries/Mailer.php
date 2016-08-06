<?php
namespace WorkFrame\Libraries;

class Mailer_exception extends \WorkFrame\Exceptions\WorkFrame_exception {}
class Mailer {
	function mail_from_template($email_addresses, $template, $vars) {
		$template_file = APP_PATH . '/email_templates/'.$template;

		if(!file_exists($template_file)) {
			throw new Mailer_exception('Could not find template', $template_file);
		}
		extract($vars);
		include($template_file);
		if(!isset($HTML_MESSAGE, $TEXT_MESSAGE, $SUBJECT)) {
			throw new Mailer_exception('Template did not declare required variables', "HTML MESSAGE:".print_r(@$HTML_MESSAGE,1)."\nTEXT MESSAGE:".print_r($TEXT_MESSAGE,1)."\nSUBJECT:".print_r($SUBJECT, 1));
		}
		$headers = [];
		
		if(isset($BCC)) {
			$headers[] = 'BCC: '.$BCC;
		}

		if(isset($FROM)) {
			$headers[] = 'FROM: '.$FROM;
		}

		$boundary = md5( uniqid() . microtime() );

		$headers[] = "MIME-Version: 1.0";

		$headers[] = "Content-Type: multipart/alternative; boundary=\"$boundary\"";

		// Plain text version of message
		$body = "--$boundary\r\n" .
		   "Content-Type: text/plain; charset=ISO-8859-1\r\n" .
		   "Content-Transfer-Encoding: base64\r\n\r\n";
		$body .= chunk_split( base64_encode($TEXT_MESSAGE));
		// HTML version of message
		$body .= "--$boundary\r\n" .
		   "Content-Type: text/html; charset=ISO-8859-1\r\n" .
		   "Content-Transfer-Encoding: base64\r\n\r\n";
		$body .= chunk_split( base64_encode( $HTML_MESSAGE ) );
		$body .= "--$boundary--";

		foreach((array)$email_addresses as $email) {
			mail($email, $SUBJECT, $body, implode("\r\n", $headers));
		}
	}


}


