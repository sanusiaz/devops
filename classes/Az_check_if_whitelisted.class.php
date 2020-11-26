<?php 
	
	// THIS CLASS FILE CHECKS IF PRODUCTS HAS BEEN WHITELISTED 

	class Az_check_if_whitelisted {
		protected $conn;
		protected $users_ref;
		protected $cart_id;


		public function __construct($conn, $prod_ref, $users_ref, $cart_id) {
			$this->conn 			= $conn;				// conenctions
			$this->prod_ref 		= $prod_ref;			// products ref
			$this->users_ref 		= $users_ref;			// users ref
			$this->cart_id 			= $cart_id;				// cart id
		}

		protected function Check_if_products_exists() {
			if ( $this->prod_ref !== "" && $this->users_ref !== "" && $this->cart_id !== "" ) {
				$this->prod_ref 		= htmlentities($this->prod_ref);
				$this->prod_ref 		= strip_tags($this->prod_ref);

				$this->users_ref 		= htmlentities($this->users_ref);
				$this->users_ref 		= strip_tags($this->users_ref);

				$this->cart_id 			= htmlentities($this->cart_id);
				$this->cart_id 			= strip_tags($this->cart_id);

				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->prod_ref) && preg_match("/^[a-zA-Z0-9 ]+$/", $this->users_ref) && preg_match("/^[a-zA-Z0-9]+$/", $this->cart_id) ) {
					$this->prod_ref 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->prod_ref);
					$this->users_ref 		= preg_replace("#[^a-zA-Z0-9 ]#", "", $this->users_ref);
					$this->cart_id 			= preg_replace("#[^a-zA-Z0-9]#", "", $this->cart_id);

					$this->prod_ref 		= mysqli_real_escape_string($this->conn, $this->prod_ref);
					$this->users_ref 		= mysqli_real_escape_string($this->conn, $this->users_ref);
					$this->cart_id 			= mysqli_real_escape_string($this->conn, $this->cart_id);


					// check if products exists in store
					if ( $Checkprod = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {
						$Checkprod->bind_param("s", $this->prod_ref);
						$Checkprod->execute();
						$Checkprod->store_result();

						if ( $Checkprod->num_rows > 0 ) {
							// products exists
							// Now we check if products exists in whitelists
							if ( $checkIfWhiteListed = $this->conn->prepare("SELECT prod_ref FROM az_ldm__whitelists WHERE prod_ref = ? AND users_ref = ? AND cart_id = ? ORDER BY id DESC LIMIT 1") ) {
								$checkIfWhiteListed->bind_param("sss", $this->prod_ref, $this->users_ref, $this->cart_id);
								$checkIfWhiteListed->execute();
								$checkIfWhiteListed->store_result();

								if ( $checkIfWhiteListed->num_rows > 0 ) {
									return true;
								}
								$checkIfWhiteListed->close(); // close all connections
							}
							else {
								echo mysqli_error($this->conn);
							}
						}

						$Checkprod->close(); // close all connection
					}
				}
			}
		}

		public function show_outputs() {
			if ( $this->Check_if_products_exists() === true ) {
				// products exists  in whitelists
				return true;
			}
		}
	}


 ?>