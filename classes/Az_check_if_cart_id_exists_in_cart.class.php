<?php 

	class Az_check_if_cart_id_exists_in_cart{
		protected $cart_id;
		protected $conn;
		protected $output_messages;
		public $all_error_messages;


		public function __construct( $conn, $cart_id, $output_messages = "", $all_error_messages = "" ) {
			$this->conn = $conn;
			$this->cart_id = $cart_id;
			$this->output_messages = $output_messages;
		}

		protected function Check_cart_id_in_cart() {
			if ( $this->cart_id !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->cart_id) ) {
					$this->cart_id = preg_replace("#[^a-zA-Z0-9]#", "", $this->cart_id);
					$this->cart_id = mysqli_real_escape_string($this->conn, $this->cart_id);

					if ( $ChCartId = $this->conn->prepare("SELECT cart_id FROM az_ldm__carts WHERE cart_id = ?") ) {
						$ChCartId->bind_param("s", $this->cart_id);
						$ChCartId->execute();
						$ChCartId->store_result();

						if ( $ChCartId->num_rows > 0 ) {
							// get all carts products 
							return true;
						}
						else {
							// get all carts products from session
							return false;
						}
					}
				}
			}
			else {
				echo $this->all_error_messages = "Cart Id Cannot Be Empty";
			}
		}

		public function Send_response() {
			if ( $this->Check_cart_id_in_cart() === true ) {
				return true;
			}
			else {
				return false;
			}
		}
	}


 ?>