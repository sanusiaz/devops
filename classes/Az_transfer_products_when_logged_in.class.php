<?php 
	
	// THIS CLASS FILE CHECKS IS USERS HAS SELECTED PRODUCTS IN CARTS OR WHITELISTS 
	// BEFORE LOGGINNG IN 
	// NOTE: when users is logged in we transfer all products to users cart id in carts store
	// so users can view products in carts and checkout
	

	class Az_transfer_products_when_logged_in {
		protected $conn;
		protected $users_ref;
		protected $old_users_cart_id;
		protected $new_users_cart_id;

		public $requestType;


		public function __construct($conn, $users_ref, $old_users_cart_id, $new_users_cart_id, $requestType = "") {
			$this->conn 				= $conn;					// conenction 
			$this->users_ref 			= $users_ref;				// users ref
			$this->old_users_cart_id 	= $old_users_cart_id;		// old users cart id before logging in
			$this->new_users_cart_id 	= $new_users_cart_id;		// users cart id after logging in / real users cart id
			$this->requestType 			= $requestType;				// request Type
		}

		protected function check_if_oldCartsID_exists_in_store() {
			if ( $this->conn !== false )  {
				if ( $this->old_users_cart_id !== "" ) {
					$this->old_users_cart_id 	= htmlentities($this->old_users_cart_id);


					$this->old_users_cart_id 	= strip_tags($this->old_users_cart_id);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->old_users_cart_id) ) {
						$this->old_users_cart_id 	= preg_replace("#[^a-zA-Z0-9]#", "", $this->old_users_cart_id);

						$this->old_users_cart_id 		= mysqli_real_escape_string($this->conn, $this->old_users_cart_id);

						if ( $this->requestType === "carts" ) {
							// check if any products exists in store
							if ( $checkProd = $this->conn->prepare("SELECT prod_ref FROM az_ldm__carts WHERE cart_id = ? ORDER BY id DESC") ) {
								$checkProd->bind_param("s", $this->old_users_cart_id);
								$checkProd->execute();
								$checkProd->store_result();

								if ( $checkProd->num_rows > 0 ) {
									return true;
								}
								else {
									return false;
								}

								$checkProd->close(); 	// close all conenctions
							}
						}

						if ( $this->requestType === "whitelists" ) {
							if ( $checkProd = $this->conn->prepare("SELECT prod_ref FROM az_ldm__whitelists WHERE cart_id = ? ORDER BY id DESC") ) {
								$checkProd->bind_param("s", $this->old_users_cart_id);
								$checkProd->execute();
								$checkProd->store_result();

								if ( $checkProd->num_rows > 0 ) {
									return true;
								}
								else {
									return false;
								}

								$checkProd->close(); 	// close all conenctions
							}
						}
					}
				}
			}
		}

		protected function update_all_old_cart_id() {
			if ( $this->check_if_oldCartsID_exists_in_store() === true ) {
				// update all carts id to new carts id
				if ( $this->old_users_cart_id !== "" && $this->new_users_cart_id && $this->users_ref !== "" ) {
					$this->old_users_cart_id 	= htmlentities($this->old_users_cart_id);
					$this->new_users_cart_id 	= htmlentities($this->new_users_cart_id);
					$this->users_ref 			= htmlentities($this->users_ref);


					$this->old_users_cart_id 	= strip_tags($this->old_users_cart_id);
					$this->new_users_cart_id 	= strip_tags($this->new_users_cart_id);
					$this->users_ref 			= strip_tags($this->users_ref);

					$this->old_users_cart_id 	= mysqli_real_escape_string($this->conn, $this->old_users_cart_id);
					$this->new_users_cart_id 	= mysqli_real_escape_string($this->conn, $this->new_users_cart_id);
					$this->users_ref 			= mysqli_real_escape_string($this->conn, $this->users_ref);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->old_users_cart_id) && preg_match("/^[a-zA-Z0-9]+$/", $this->new_users_cart_id) && preg_match("/^[a-zA-Z0-9 ]+$/", $this->users_ref) ) {

						$this->old_users_cart_id 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->old_users_cart_id);
						$this->new_users_cart_id 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->new_users_cart_id);
						$this->users_ref 				= preg_replace("#[^a-zA-Z0-9]#", "", $this->users_ref);

						if ( $this->conn !== false ) {
							if ( $updateCartsID = $this->conn->prepare("UPDATE az_ldm__carts SET cart_id = ?, users_ref = ? WHERE cart_id = ? ORDER BY id DESC") ) {
								$updateCartsID->bind_param("sss", $this->new_users_cart_id, $this->users_ref, $this->old_users_cart_id);
								if ( $updateCartsID->execute() !== true ) {
									die("An Error Occured In Updating Carts Products. Please Try Again Later");
								}
								$updateCartsID->close(); /// close all conenctions
							}
						}

					}

				}
			}
		}

		protected function update_all_old_whitelists_carts_id() {
			if ( $this->check_if_oldCartsID_exists_in_store() === true ) {
				// update all carts id to new carts id
				if ( $this->old_users_cart_id !== "" && $this->new_users_cart_id && $this->users_ref !== "" ) {
					$this->old_users_cart_id 	= htmlentities($this->old_users_cart_id);
					$this->new_users_cart_id 	= htmlentities($this->new_users_cart_id);
					$this->users_ref 			= htmlentities($this->users_ref);


					$this->old_users_cart_id 	= strip_tags($this->old_users_cart_id);
					$this->new_users_cart_id 	= strip_tags($this->new_users_cart_id);
					$this->users_ref 			= strip_tags($this->users_ref);

					$this->old_users_cart_id 	= mysqli_real_escape_string($this->conn, $this->old_users_cart_id);
					$this->new_users_cart_id 	= mysqli_real_escape_string($this->conn, $this->new_users_cart_id);
					$this->users_ref 			= mysqli_real_escape_string($this->conn, $this->users_ref);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->old_users_cart_id) && preg_match("/^[a-zA-Z0-9]+$/", $this->new_users_cart_id) && preg_match("/^[a-zA-Z0-9 ]+$/", $this->users_ref) ) {

						$this->old_users_cart_id 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->old_users_cart_id);
						$this->new_users_cart_id 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->new_users_cart_id);
						$this->users_ref 				= preg_replace("#[^a-zA-Z0-9 ]#", "", $this->users_ref);

						if ( $this->conn !== false ) {
							if ( $updateCartsID = $this->conn->prepare("UPDATE az_ldm__whitelists SET cart_id = ?, users_ref = ? WHERE cart_id = ? ORDER BY id DESC") ) {
								$updateCartsID->bind_param("sss", $this->new_users_cart_id, $this->users_ref, $this->old_users_cart_id);
								if ( $updateCartsID->execute() !== true ) {
									die("An Error Occured In Updating Whitelists Products. Please Try Again Later");
								}
								$updateCartsID->close(); /// close all conenctions
							}
						}

					}

				}
			}
		}


		protected function get_users_cart_id() {
			if ( $this->users_ref !== "" ) {
				$this->users_ref = htmlentities($this->users_ref);
				$this->users_ref = strip_tags($this->users_ref);

				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->users_ref) ) {
					$this->users_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->users_ref);

					if ( $this->conn !== false ) {
						$this->users_ref = mysqli_real_escape_string($this->conn, $this->users_ref);

						if ( $stmt = $this->conn->prepare("SELECT users_cart_id FROM az_ldm__users WHERE users_ref = ? ORDER BY id DESC LIMIT 1") ) {
							$stmt->bind_param("s", $this->users_ref);
							$stmt->execute();
							$stmt->store_result();

							if ( $stmt->num_rows > 0 ) {
								$stmt->bind_result($users_cart_id);
								$stmt->fetch();

								$cart_id = $users_cart_id;

								$cart_id = htmlentities($cart_id);
								$cart_id = strip_tags($cart_id);

								if ( preg_match("/^[a-zA-Z0-9]+$/", $cart_id) ) {
									$cart_id = preg_replace("#[^a-zA-Z0-9]#", "", $cart_id);
									$cart_id = mysqli_real_escape_string($this->conn, $cart_id);

									$this->new_users_cart_id = $cart_id;
									return true;
								}

							}
							$stmt->close(); 	// CLOSE ALL CONNECTIONS
						}
					}
				} 
			}
		}


		public function show_all_outputs() {
			$this->requestType = strtolower($this->requestType);
			if ( $this->requestType === "carts" ) {
				$this->check_if_oldCartsID_exists_in_store(); // get all products from carts
			} 
			if ( $this->requestType === "whitelists" ) {
				$this->check_if_oldCartsID_exists_in_store(); // get all products from whitelists
			}
		}

		public function users_cart_id() {
			if ( $this->get_users_cart_id() === true ) {
				return $this->new_users_cart_id;
			}
		}

		public function update_cart_id() {
			if ( $this->requestType === "carts" ) {
				$this->update_all_old_cart_id(); // get all products from carts
			} 
			if ( $this->requestType === "whitelists" ) {
				$this->update_all_old_whitelists_carts_id(); // get all products from whitelists
			}
		}
	}



 ?>