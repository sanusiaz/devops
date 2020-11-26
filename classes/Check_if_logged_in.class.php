<?php
	class Check_if_logged_in{
		protected $users_ref;
		protected $conn;

		public function __construct($users_ref, $conn) {
			$this->users_ref = $users_ref;
			$this->conn = $conn;
		}

		public function check_users() {
			if ( isset($_SESSION['az_ldm_login']) && $_SESSION['az_ldm_login'] === true && isset($_SESSION['users_ref']) && !empty($_SESSION['users_ref']) ) {
				// check if users exists in database
				if ( $stmp_check_users = $this->conn->prepare("SELECT username FROM az_ldm__users WHERE users_ref = ? ORDER BY id DESC LIMIT 1") ) {
					$stmp_check_users->bind_param('s', $this->users_ref);
					$stmp_check_users->execute();
					$stmp_check_users->store_result();

						if ( $stmp_check_users->num_rows > 0 ) {
							$stmp_check_users->bind_result($username);
							$stmp_check_users->fetch(); 
							// users is valid
							return true;
						}
						else {
							// users is invalid
							return false;
						}
					$stmp_check_users->close();
				}
				else {
					$error['server_error'] .= "Error in checking if users is login";
				}
			}
		}

		public function Get_users_cart_id() {
			if ( $this->check_users() === true ) {
				// get users cart id
				if ( $GetCartId = $this->conn->prepare("SELECT users_cart_id FROM az_ldm__users WHERE users_ref = ? ORDER BY id DESC LIMIT 1") ) {
					$GetCartId->bind_param("s", $this->users_ref);
					$GetCartId->execute();
					$GetCartId->store_result();

					if ( $GetCartId->num_rows > 0 ) {
						$GetCartId->bind_result($users_cart_id);
						$GetCartId->fetch();

						if ( preg_match("/^[a-zA-Z0-9]+$/", $users_cart_id) ) {
							$users_cart_id = preg_replace("#[^a-zA-Z0-9]#", "", $users_cart_id);
							$users_cart_id = mysqli_real_escape_string($this->conn, $users_cart_id);
							return $users_cart_id;
						}
					}
					$GetCartId->close(); // close all connections
				}
			}
		}
	}
