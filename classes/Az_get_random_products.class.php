<?php 
	/**
	* THIS CLASS FILE SELECTS RANDOM PRODUCTS FROM STORE
	**/
	class Az_get_random_products {
		protected $conn;				// connection
		protected $output_messages;		// output messages
		public $error_messages;			// error messages

		public function __construct($conn) {
			$this->conn = $conn;
			$this->output_messages = $output_messages = "";
			$this->error_messages = $error_messages = "";
		}

		protected function get_random_products() {
			if ( $this->conn !== false ) {
				// check if any products exists in store
				if ( $checkProducts = $this->conn->prepare("SELECT prod_category FROM az_ldm__products WHERE prod_ref IS NOT NULL ORDER BY RAND() DESC LIMIT 1") ) {
					if ( $checkProducts->execute() === true ) {
						$checkProducts->store_result();

						if ( $checkProducts->num_rows > 0 ) {
							$checkProducts->bind_result($prod_category);
							$checkProducts->fetch();

							if ( $prod_category !== "" && preg_match("/^[a-zA-Z0-9]+$/", $prod_category) ) {
								// selcet random 20 products
								// get ranmdom 20 results from store
								$resultsLimits = 20;
								$sql_select_rnd_products = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products ORDER BY RAND() LIMIT {$resultsLimits}";

								$qry_results = mysqli_query($this->conn, $sql_select_rnd_products);
								if ( $qry_results ) {
									if ( mysqli_num_rows($qry_results) > 0 ) {
										$results_count = 0;
										$this->output_messages = [];
										while ( $row = mysqli_fetch_assoc($qry_results) ) {
											$results_count++;
											if ( $results_count <= ( $resultsLimits ) ) {
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

												$row['pics_up_names'] 		= mysqli_real_escape_string($this->conn, $pics_up_names);				// pics tmp name
												$row['pics_up_real_name'] 	= mysqli_real_escape_string($this->conn, $pics_real_name);				// pics real name
												$row['post_title'] 			= mysqli_real_escape_string($this->conn, $row['post_title']);			// posts title
												$row['prod_ref'] 			= mysqli_real_escape_string($this->conn, $row['prod_ref']);				// prod ref
												$row['imageType'] 			= mysqli_real_escape_string($this->conn, $imageType);					// image type
												$row['prod_prices'] 		= mysqli_real_escape_string($this->conn, $row['prod_prices']);			// products prices
												$row['discount_price'] 		= mysqli_real_escape_string($this->conn, $row['discount_price']);		// dicsount price
												$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage

												$this->output_messages[] = $row;
											}
										}
										return true;
									}
									else {
										$this->all_error_messages = "No Products Found";
									}
								}
								else {
									echo mysqli_error($this->conn);
								}
							}
						}
					}
				}
			}
		}
		
		/**
		* SHOW ALL RANDOM PRODUCTS FROM OUTPUT MESSAGES RESULTS
		**/
		public function show_all_rnd_products() {
			if ( $this->get_random_products() === true ) {
				if ( is_array($this->output_messages) && ( count($this->output_messages)  > 0) ) {
					return $this->output_messages;
				}
			}
		}
	}


 ?>
