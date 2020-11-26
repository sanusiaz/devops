<?php 
	/**
	 * THIS SCRIPTS GET ALL COMMENTS FOR EACH PRODUCTS VIA PROD_REF/ID
	 */
	
	class Az_get_all_products_comments {
		protected $conn;
		protected $prod_ref;
		protected $output_messages = [];

		public function __construct( $conn, $prod_ref, $output_messages = [] ) {
			$this->conn 				= $conn;
			$this->prod_ref 			= $prod_ref;
			$this->output_messages 		= $output_messages;
		}

		protected function Get_all_products_comments () {
			if ( $this->conn !== false ) {

				$this->prod_ref 	= htmlentities($this->prod_ref);
				$this->prod_ref 	= strip_tags($this->prod_ref);

				if ( $this->prod_ref !== "" && preg_match("/^[a-zA-Z0-9]+$/", $this->prod_ref) ) {
					$this->prod_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->prod_ref);
					$this->prod_ref = mysqli_real_escape_string($this->conn, $this->prod_ref);


					// SHOW TOP 20 COMMENTS
					if ( $stmt = $this->conn->prepare("SELECT prod_ref, firstName, lastName, commentDate, timeMeridian, message FROM az_ldm__products_comments WHERE prod_ref = ? ORDER BY id DESC LIMIT 20") ) {

						$stmt->bind_param("s", $this->prod_ref);
						$stmt->execute();
						$stmt->store_result();

						if ( $stmt->num_rows > 0 ) {
							$stmt->bind_result($prod_ref, $firstName, $lastName, $commentDate, $timeMeridian, $message);
							$stmt->fetch();
							$prod_ref = mysqli_real_escape_string($this->conn, $prod_ref);

							$sql = "SELECT firstName, lastName, commentDate, timeMeridian, message FROM az_ldm__products_comments WHERE prod_ref = '$prod_ref' ORDER BY id DESC LIMIT 20";
							$qry= mysqli_query($this->conn, $sql);

							if ( $qry ) {
								if ( mysqli_num_rows($qry) > 0 ) {
									while ($row = mysqli_fetch_assoc($qry)) {
										$row['firstName'] 		= mysqli_real_escape_string($this->conn, $row['firstName']);
										$row['lastName'] 		= mysqli_real_escape_string($this->conn, $row['lastName']);
										$row['timeMeridian'] 	= mysqli_real_escape_string($this->conn, $row['timeMeridian']);

										$this->output_messages[] = $row;
									}

									return true;
								}
							}
						}
						$stmt->close();	// close connections
					}
				}
			} 
		}

		public function show_comments_outputs() {
			if ( $this->prod_ref !== "" ) {
				if ($this->Get_all_products_comments() === true ) {
					return $this->output_messages;
				}
				else {
					// show error message
					return false;
				}

			}
		}

	}

 ?>