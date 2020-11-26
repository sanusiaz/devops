<?php 
	/**
	 * THIS SCRIPT CHECK IF CAPTCH EXISTS IN DATABASE
	 * Note: users captch length should be max of 10 characters
	 */
	class Check_captch{
		protected $captched_text;
		public $error_message;


		public function __construct($captched_text, $error_message = "") {
			$this->captched_text 	= $captched_text;
			$this->error_message 	= $error_message;
		}
		public function get_mathched__captch($conn, $captch_text) {
			if ( $this->captched_text !== "" && strlen($this->captched_text) >= 10 ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->captched_text) ) {
					$this->captched_text = preg_replace("#[^a-zA-Z0-9]#", "", $this->captched_text);
					if ( $captch_st = $conn->prepare("SELECT captch_text FROM az_ldm__captch_field WHERE used=1 AND expired_captch=0 AND captch_text=? ORDER BY id DESC LIMIT 1") ) {
						$captch_st->bind_param('s', $this->captched_text);		// bind param
						$captch_st->execute();									// execute query
						$captch_st->store_result();								// store result

						// check if aptch exists
						if ( $captch_st->num_rows > 0 ) {
							$captch_st->bind_result($captch_text);
							$captch_st->fetch();


							return true;
						}
						else {
							// error message
							$this->error_message = "Captch does not exists reload page";
							// captch does not exists
							return false;
						}

						$captch_st->close();		// close all connections
					}
					else {
						// error message
						$this->error_message = "An Error Occured";
					}
				}
			}
		}
	}


 ?>