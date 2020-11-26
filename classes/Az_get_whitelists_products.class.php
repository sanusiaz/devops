<?php 

	class Az_get_whitelists_products{
		protected $cart_id;
		protected $conn;
		protected $output_messages;
		public $all_error_messages;
		public $results_count;
		protected $show_products;


		public function __construct( $conn, $cart_id, $show_products = true) {
			$this->conn = $conn;
			$this->cart_id = $cart_id;
			$this->output_messages = $output_messages = [];
			$this->all_error_messages = $all_error_messages = "";
			$this->results_count = $results_count = 0;
			$this->show_products = $show_products;
		}

		protected function get_all_products_carts() {
			if ( $this->cart_id !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->cart_id) ) {
					$this->cart_id = preg_replace("#[^a-zA-Z0-9]#", "", $this->cart_id);
					$this->cart_id = mysqli_real_escape_string($this->conn, $this->cart_id);
					$cart_id = $this->cart_id;

					// get all products from cart id
					$productsOrderStatus = 0;
					if ( $GetAllCartsProducts = $this->conn->prepare("SELECT prod_ref FROM az_ldm__whitelists WHERE cart_id = ? ORDER BY id DESC LIMIT 1") ) {
						$GetAllCartsProducts->bind_param("s", $cart_id);
						$GetAllCartsProducts->execute();
						$GetAllCartsProducts->store_result();

						if ( $GetAllCartsProducts->num_rows > 0 ) {
							$GetAllCartsProducts->bind_result($prod_ref);
							$GetAllCartsProducts->fetch();

							$sql_get_all_carts_products = "SELECT prod_ref FROM az_ldm__whitelists WHERE cart_id = '$cart_id' ORDER BY id DESC";
							$qry_select_whitelists_products = mysqli_query($this->conn, $sql_get_all_carts_products);

							if ( $qry_select_whitelists_products ) {
								// total no of carts last 24 hrs
								$carts_results = mysqli_num_rows($qry_select_whitelists_products); // results count

								if ( mysqli_num_rows($qry_select_whitelists_products) > 0 ) {
									while ($row = mysqli_fetch_assoc($qry_select_whitelists_products)) {
										$prod_ref_check = $row['prod_ref'];		// products ref

										// get all products info
										$sql_select_all_products_via_carts = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage, prod_quantity, prod_size, prod_color FROM az_ldm__products WHERE prod_ref = '$prod_ref_check' ORDER BY id DESC LIMIT 1";
										$qry_select_all_products = mysqli_query($this->conn, $sql_select_all_products_via_carts);

										if ( $qry_select_all_products ) {
											// products exists get no of products
											$this->results_count = $carts_results;		// results count

											if ( $this->show_products === true ) {
												if ( mysqli_num_rows($qry_select_all_products) > 0 ) {
													while ($row = mysqli_fetch_assoc($qry_select_all_products)) {
														$row['pics_up_names'] 		= unserialize($row['pics_up_names']);			// pics tmp name
														$row['pics_up_real_name'] 	= unserialize($row['pics_up_real_name']);		// pics real name
														if ( is_array($row['pics_up_names']) && is_array($row['pics_up_real_name']) ) {
															// show tyhe first pics 
															$pics_up_names 	= $row['pics_up_names'][0]; 
															$pics_real_name = $row['pics_up_real_name'][0];
														}
														else {
															$pics_up_names 	= $row['pics_up_names'];
															$pics_real_name = $row['pics_up_real_name'];
														}
														// get image type from real image pics name
														$imageType = explode(".", $pics_real_name);
														$imageType = array_pop($imageType);

														$row['pics_up_names'] 		= mysqli_real_escape_string($this->conn, $pics_up_names);			// pics tmp name
														$row['pics_up_real_name'] 	= mysqli_real_escape_string($this->conn, $pics_real_name);			// pics real name
														$row['post_title'] 			= mysqli_real_escape_string($this->conn, $row['post_title']);		// posts title
														$row['prod_ref'] 			= mysqli_real_escape_string($this->conn, $row['prod_ref']);			// prod ref
														$row['imageType'] 			= mysqli_real_escape_string($this->conn, $imageType);				// image type
														$row['prod_prices'] 		= mysqli_real_escape_string($this->conn, $row['prod_prices']);		// products prices
														$row['discount_price'] 		= mysqli_real_escape_string($this->conn, $row['discount_price']);	// dicsount price
														$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// dicsount percentage

														$row['prod_quantity'] 		= mysqli_real_escape_string($this->conn, $row['prod_quantity']);		// products quantities

														$this->output_messages[] = $row;
													}
												}
											}
										}
									}
								}
							}
							else {
								echo mysqli_error($this->conn);
							}
						}
						$GetAllCartsProducts->close(); // close all connections
					}
				}
			}
			else {
				echo $this->all_error_messages = "Cart Id Cannot Be Empty";
			}
		}

		public function show_outputs() {
			$this->get_all_products_carts();
			if ( $this->show_products === true ) {
				return $this->output_messages;
			}
			else {
				return $this->results_count;
			}
		}
	}


 ?>