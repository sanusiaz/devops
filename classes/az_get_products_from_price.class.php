<?php 
	
	class Az_get_products_from_price {
		protected $starting_price;
		protected $ending_price;
		protected $conn;
		protected $output_messages;

		public function __construct($starting_price, $ending_price, $conn) {
			$this->starting_price 	= $starting_price;
			$this->ending_price 	= $ending_price;
			$this->conn 			= $conn;

			$this->output_messages = $output_messages = [];
		}


		protected function getAllProductsFromPriceDiff() {
			echo "Done GSDFyued";
			if ( !empty($this->starting_price) && !empty($this->ending_price) ) {
				$this->re_calc_price();		// recalculate price

				if ( $this->starting_price > $this->ending_price ) {
					$starting_price = $this->starting_price;
					$ending_price 	= $this->ending_price;
				}
				else {
					$starting_price = $this->ending_price;
					$ending_price 	= $this->starting_price;
				}

				// GET ALL PRODUCTS FROM STORE USING PRICE RANGE
		
				$limitedQuantity = 10; // limited products quantity to display

				if ( preg_match("/^[a-zA-Z0-9]+$/", $limitedQuantity) ) {

					$limitedQuantity = preg_replace("#[^a-zA-Z0-9]#", "", $limitedQuantity);
					$limitedQuantity = mysqli_real_escape_string($this->conn, $limitedQuantity);

					if ( $GetMonthlyDeals = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_price BETWEEN ? AND ?  ORDER BY id DESC LIMIT 10") ) {
						$GetMonthlyDeals->bind_param('ss', $starting_price, $ending_price);
						$GetMonthlyDeals->execute();
						$GetMonthlyDeals->store_result();

						if ( $GetMonthlyDeals->num_rows > 0 ) {
							$GetMonthlyDeals->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
							$GetMonthlyDeals->fetch();

							$pics_up_names 		= mysqli_real_escape_string($this->conn, $pics_up_names);
							$pics_up_real_name 	= mysqli_real_escape_string($this->conn, $pics_up_real_name);
							$post_title 		= mysqli_real_escape_string($this->conn, $post_title);
							$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);

							// if cat is greated than 12 show one product per cat
							$sql_check_val_query = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products WHERE prod_price BETWEEN '$starting_price' AND '$ending_price' ORDER BY id DESC LIMIT 10";
							$qry_sql_check_val_query = mysqli_query($this->conn, $sql_check_val_query);
							if ( mysqli_num_rows($qry_sql_check_val_query) > 0 ) {
								while ($row = mysqli_fetch_assoc($qry_sql_check_val_query)) {
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
									$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage
									$this->output_messages[] = $row;
									print_r($row);
								}
							}
						}

						$GetMonthlyDeals->close();
					}
				}
			}
			else {
				echo "Price Range Is Empty";
			}
		}
		public function re_calc_price() {
			$this->starting_price 	= re_calc_prod_price($this->starting_price);
			$this->ending_price 	= re_calc_prod_price($this->ending_price);
		}
	}


?>