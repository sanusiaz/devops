<?php 
	/**
	 * THIS CLASS GET ALL PRODUCTS SINCE LAST MONTH THAT ARE LIMITED i.e only one quantity left
	 */
	class Az_get_deals_month {
		protected $conn;
		protected $output_messages;
		protected $resultsLimit;

		public function __construct($conn, $resultsLimit = 6) {
			$this->conn = $conn;
			$this->output_messages = $output_messages = [];
			$this->results_limits = $resultsLimit;
		}
		protected function GetMonthlyDeals_onLimitedQuantity() {
			if ( $this->conn ) {
				/**
				 * SHOW TOP PRORDUCTS FOR LAST MONTH
				 */
				$TodaysDate 			= date('Y-m-d');				// todays date
				$ThisYear 				= date('Y');					// this year date i.e 2020
				$LastMonth 				= date('m') - 1;				// Last Month

				$LastFullMonthDate 		= date("Y-$LastMonth-d"); 	// last five years date
				$limitedQuantity = 10; // limited products quantity to display

				if ( preg_match("/^[a-zA-Z0-9]+$/", $limitedQuantity) ) {

					$limitedQuantity = preg_replace("#[^a-zA-Z0-9]#", "", $limitedQuantity);
					$limitedQuantity = mysqli_real_escape_string($this->conn, $limitedQuantity);

					$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $this->results_limits);
					$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);

					if ( $GetMonthlyDeals = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? AND prod_quantity <= ? ORDER BY id DESC LIMIT 1") ) {
						$GetMonthlyDeals->bind_param('ssi', $LastFullMonthDate, $TodaysDate, $limitedQuantity);
						$GetMonthlyDeals->execute();
						$GetMonthlyDeals->store_result();

						if ( $GetMonthlyDeals->num_rows > 0 ) {
							$GetMonthlyDeals->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
							$GetMonthlyDeals->fetch();

							$pics_up_names 		= mysqli_real_escape_string($this->conn, $pics_up_names);
							$pics_up_real_name 	= mysqli_real_escape_string($this->conn, $pics_up_real_name);
							$post_title 		= mysqli_real_escape_string($this->conn, $post_title);
							$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);

							// get random 50 results from store
							$sql_check_val_query = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products WHERE prod_upload_date BETWEEN '$LastFullMonthDate' AND '$TodaysDate'AND prod_quantity <= '$limitedQuantity'  ORDER BY RAND() LIMIT 50";
							$qry_sql_check_val_query = mysqli_query($this->conn, $sql_check_val_query);
							if ( mysqli_num_rows($qry_sql_check_val_query) > 0 ) {
								$results_count = 0;
								while ($row = mysqli_fetch_assoc($qry_sql_check_val_query)) {
									$results_count++;
									if ( $results_count <= ( $resultsLimit ) ) {
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
									}
								}
							}
						}

						$GetMonthlyDeals->close();
					}
				}
			}
		}

		public function show_outputs() {
			$this->GetMonthlyDeals_onLimitedQuantity();
			if ( count($this->output_messages > 0) ) {
				return $this->output_messages;
			}
			else {
				echo "\nNo Output Gotten";
			}
		}
	}


 ?>