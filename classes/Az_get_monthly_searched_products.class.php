<?php 
		
	class Az_get_monthly_searched_products {
		protected $conn;
		protected $output_messages;
		protected $limitedDicsountPercentage;
		protected $limitedResults;

		public function __construct($conn, $limitedDicsountPercentage = 10, $limitedResults = 6) {
			$this->conn 						= $conn;
			$this->output_messages 				= $output_messages = [];
			$this->limitedDicsountPercentage 	= $limitedDicsountPercentage;
			$this->limitedResults 				= $limitedResults;
		}
		protected function GetLastTwoDaysDeals_onlimitedDicsountPercentage() {
			if ( $this->conn ) {
				/* SHOW TOP PRORDUCTS FOR LAST 2 MONTH */
				$TodaysDate 			= date('Y-m-d');			// todays date
				$Last2Month				=  date('m') - 2;			// last 2 Month
				$Last2Month 			= date("Y-$Last2Month-d"); 	// last 2 Month date

				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->limitedDicsountPercentage) ) {

					$resultsLimit = $this->limitedResults;
					$limitedDicsountPercentage 	= preg_replace("#[^a-zA-Z0-9]#", "", $this->limitedDicsountPercentage);
					$limitedDicsountPercentage 	= mysqli_real_escape_string($this->conn, $limitedDicsountPercentage);
					$resultsLimit				= mysqli_real_escape_string($this->conn, $resultsLimit);

					if ( $GetLastTwoDaysDeals = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products_logs WHERE prod_log_date BETWEEN ? AND ? AND discount_percentage BETWEEN 0 AND '$limitedDicsountPercentage' ORDER BY id DESC LIMIT 1") ) {
						$GetLastTwoDaysDeals->bind_param('ss', $Last2Month, $TodaysDate);
						$GetLastTwoDaysDeals->execute();
						$GetLastTwoDaysDeals->store_result();

						if ( $GetLastTwoDaysDeals->num_rows > 0 ) {
							$GetLastTwoDaysDeals->bind_result($prod_ref);
							$GetLastTwoDaysDeals->fetch();

							$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);			// products reference

							// show random 50 results
							$sql_check_val_query = "SELECT prod_ref FROM az_ldm__products_logs WHERE prod_log_date BETWEEN '$Last2Month' AND '$TodaysDate' AND discount_percentage BETWEEN 0 AND '$limitedDicsountPercentage' ORDER BY RAND() DESC LIMIT 50";
							$qry_sql_check_val_query = mysqli_query($this->conn, $sql_check_val_query);
							if ( mysqli_num_rows($qry_sql_check_val_query) > 0 ) {
								$results_count = 0;
								while ( $row = mysqli_fetch_assoc($qry_sql_check_val_query) ) {
									$results_count++;
									if ( $results_count <= ( $resultsLimit ) ) {
										$Ch_prod_ref = $row['prod_ref'];
										$sql_check_val_query_re = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products WHERE prod_ref = '$Ch_prod_ref' ORDER BY id DESC LIMIT 1";

										$qry_results = mysqli_query($this->conn, $sql_check_val_query_re);
										if ( $qry_results ) {
											if ( mysqli_num_rows($qry_results) > 0 ) {
												while ( $row = mysqli_fetch_assoc($qry_results) ) {
													
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
										}
									}
								}
							}
						}
						$GetLastTwoDaysDeals->close();		// close conenctions
					}
				}
			}
		}

		public function getDiscoutPercentage() {
			return $this->limitedDicsountPercentage;
		}

		public function show_outputs_results() {
			$this->GetLastTwoDaysDeals_onlimitedDicsountPercentage();
			if ( count($this->output_messages > 0) ) {
				return $this->output_messages;
			}
			else {
				echo "\nNo Output Results!!!";
			}
		}
	}


 ?>