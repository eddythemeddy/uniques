<?php

use Postmark\PostmarkClient;

class MailHelper extends View {

	const FROM_MAIL = 'info@scouty.io';
	const FROM_NAME = 'Talenitics';
	const API_KEY = '9090717b-61d1-4e63-b6e4-c6c3246acbce';
	const EMAIL = 'info@scouty.io';

	private $logoImg = 'http://scouty.io/assets/public/img/logo-lg-blue.png';
	private $client;
	private $to = '';
	private $toName = '';
	private $subject = '';
	private $content = '';
	private $template = 'index';
	private $image = '';
	private $salutation = '';
	private $mailbody = '';
	private $cta = '';
	private $ctaLink = '';
	private $html = '';

	function __construct() {
		$this->client = new PostmarkClient(self::API_KEY);
	}

	public function send() {

		if(empty($this->to) || empty($this->toName) || empty($this->subject) || empty($this->content)) {
			return false;
		}

		$message = [
			'To' => $this->toName,
			'From' => self::EMAIL,
			'TrackOpens' => true,
			'Subject' => $this->subject,
			'TextBody' => $this->content,
			'HtmlBody' => $this->content,
			'Tag' => "New Year's Email Campaign"	    
	    ];
		
		$sendResult = $this->client->sendEmailBatch([$message]);
		return true;
	}

	public function setTo($str) {

		$this->to = $str;
	}

	public function setBody($str) {

		$this->content = $str;
	}

	public function setToName($str) {

		$this->toName = $str . '<' . $this->to . '>';
	}

	public function setSubject($str) {

		$this->subject = $str;
	}
}