<?php 
	
	// this class file checks if products exists in carts
	
	class Az_check_if_products_exists_in_cart {
		protected $conn;
		protected $prod_ref;
		protected $users_ref;
		protected $cart_id;

		public function __construct($conn, $prod_ref, $users_ref = "", $cart_id = "") {
			$this->conn = $conn;
			$this->prod_ref = $prod_ref;
			$this->users_ref = $users_ref;
			$this->cart_id = $cart_id;
		}

		protected function Check_if_products_exists_in_carts() {
			if ( $this->prod_ref !== "" && $this->cart_id !== "" && $this->users_ref !== "" ) {
				$this->prod_ref 		= htmlentities($this->prod_ref);
				$this->prod_ref 		= strip_tags($this->prod_ref);

				$this->cart_id 			= htmlentities($this->cart_id);
				$this->cart_id 			= strip_tags($this->cart_id);

				$this->users_ref 		= htmlentities($this->users_ref);
				$this->users_ref 		= strip_tags($this->users_ref);
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->prod_ref) && preg_match("/^[a-zA-Z0-9]+$/", $this->cart_id) && preg_match("/^[a-zA-Z0-9 ]+$/", $this->users_ref) ) {
					$this->prod_ref 	= preg_replace("#[^a-zA-Z0-9]#", "", $this->prod_ref);
					$this->cart_id 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->cart_id);
					$this->users_ref 	= preg_replace("#[^a-zA-Z0-9 ]#", "", $this->users_ref);

					if ( $this->conn !== false ) {
						$this->prod_ref 	= mysqli_real_escape_string($this->conn, $this->prod_ref);
						$this->cart_id 		= mysqli_real_escape_string($this->conn, $this->cart_id);
						$this->users_ref 	= mysqli_real_escape_string($this->conn, $this->users_ref);

						// we check if products exists in store
						if ( $checkIfExists = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {
							$checkIfExists->bind_param("s", $this->prod_ref);
							$checkIfExists->execute();
							$checkIfExists->store_result();

							if ( $checkIfExists->num_rows > 0 ) {
								// products exists in store
								// now we check if products exists in carts
								if ( $checkCarts = $this->conn->prepare("SELECT prod_ref FROM az_ldm__carts WHERE prod_ref = ? AND cart_id = ? AND users_ref = ? ORDER BY id DESC LIMIT 1") ) {
									$checkCarts->bind_param("sss", $this->prod_ref, $this->cart_id, $this->users_ref);
									$checkCarts->execute();
									$checkCarts->store_result();

									if ( $checkCarts->num_rows > 0 ) {
										// products exists in carts
										return true;
									}
									$checkCarts->close();	// close connections
								}
							}
							$checkIfExists->close();		// close all conenctions
						}
					}
				}
			}
		}

		public function show_outputs() {
			if ( $this->Check_if_products_exists_in_carts() === true ) {
				return true;
			} 
			else {
				return false;
			}
		}
	}

 ?>