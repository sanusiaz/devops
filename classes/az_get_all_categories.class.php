<?php 
	interface GetCat {
		public function show_Cat_outputs();
	}

	class Az_get_all_categories extends Az_get_all_cat_and_products implements GetCat {
		protected $conn;
		protected $products_ref;
		public $all_error;
		protected $output_messages = [];

		public function __construct($conn, $products_ref, $output_messages = [], $all_error = ""){
			$this->conn 			= $conn;				// connection
			$this->products_ref 	= $products_ref;		// products ref
			$this->all_error 		= $all_error;			// all error messages holder
			$this->output_messages 	= $output_messages;		// output messages
		}

		/**
		 * check if products exists in database using prod ref
		 * @return [bool]  [true for products exists ]
		 */
		protected function ProductsCheck() {
			if ( $this->products_ref !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->products_ref);
					if ( $ChProducts = $this->conn->prepare("SELECT post_title, prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {

						$ChProducts->bind_param("s", $this->products_ref);
						$ChProducts->execute();
						$ChProducts->store_result();

						if ( $ChProducts->num_rows > 0 ) {
							return true;
						}
						else {
							return false;
						}
						$ChProducts->close();
					}
				}
			}
		}

		/**
		 * CHECK IF PRODUCTS EXISTS IN STORE USING PROD REF
		 */
		function CheckProducts() {
			return $this->ProductsCheck();
		}

		protected function Get_all_cat() {
			// check if selected products exists
			if ( $this->CheckProducts() === true ) {
				$cat_show_home = 1;
				if ( $stmp = $this->conn->prepare("SELECT cat_name FROM az_ldm__categories WHERE cat_show_home = ? ORDER BY id DESC")) {
					$stmp->bind_param("i", $cat_show_home);
					$stmp->execute();
					$stmp->store_result();

					if ( $stmp->num_rows > 0 ) {
						$stmp->bind_result($cat_name);
						$stmp->fetch();
						$cat_name = mysqli_real_escape_string($this->conn, $cat_name);

						$sql_cat_select = "SELECT cat_name FROM az_ldm__categories WHERE cat_show_home = '$cat_show_home' ORDER BY id DESC LIMIT 5";
						$qry_cat_select = mysqli_query($this->conn, $sql_cat_select);
						if ( mysqli_num_rows($qry_cat_select) > 0 ) {
							while ($row = mysqli_fetch_assoc($qry_cat_select)) {
								array_push($this->output_messages, $row);
							}
						}

					} 
					$stmp->close();// close conenctions
				} 
			}
		}

		/**
		 * Show categories outputs
		 * @return [type] [outputs messages i.e cat names]
		 */
		public function show_Cat_outputs() {
			$this->Get_all_cat();
			if ( $this->output_messages !== "" && is_array($this->output_messages) ) {
				if ( count($this->output_messages) > 0 ) {
					shuffle($this->output_messages);
					return $this->output_messages;
				}
			}
		}
	}

 ?>