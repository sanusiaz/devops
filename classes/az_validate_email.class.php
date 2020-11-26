<?php

	 /*************************************************/
	 /* THIS CLASS IS TO VERIFY USERS EMAIL ADDRESS */
	 /**********************************************/

	class Az_validate_email {
		protected $email;
		protected $token;
		public $errorMessage; // error message
		public $successMessage; // success message

		public function __construct($token, $email, $errorMessage = "", $successMessage = "") {
			$this->email = $email;
			$this->token = $token;
			$this->errorMessage = $errorMessage;
			$this->successMessage = $successMessage;
		}

			public function az_val_email_address() {
				/* start email address validation */
				// This function below is decleared in all_functions page
				$email_verify = getMatchedTokenForvalidation($this->token, $this->email);
				if ( isset($email_verify) && is_bool($email_verify) && $email_verify === true ) {
					$this->successMessage = "Email has been verified";
					$email_verified_token = echo_generate_random_string(20);
					$_SESSION['email_verified_token'] = $email_verified_token;
					return true;
				}
				else {
					$this->errorMessage = "Enter A Valid Email Address ". $this->email;
					return false;
				}
			}
	}