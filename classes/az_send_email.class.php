<?php
	class Az_send_email {
		private $email;
		public $message;
		private $token;

		public function __construct($email, $message, $token) {
			$this->email = $email;
			$this->message = $message;
			$this->token = $token;
		}

		public function getMatchedToken($token) {
			if ( $this->token === $_SESSION['email_token'] ) {
				// send message to users
				
				// check if emsil is valid
				if ( $this->email !== "" ) {
					// validate email address
					if ( preg_match("/^[^@]+@[^@]+$/", $this->email) ) {
						// send message to recievers email here
					}
					else {
						$error_message = "Please Enter A Valid Email Address";
					}
				}
			} 
		}
	}
