<?php 
		
	class Az_get_products_from_collections {
		protected $conn;					// connections
		protected $output_messages;			// output messages
		protected $collection_name;			// collection name
		protected $limitedResults;			// limited results to show

		public function __construct($conn, $collection_name, $limitedResults = 6) {
			$this->conn = $conn;
			$this->output_messages = $output_messages = [];
			$this->collection_name = $collection_name;
			$this->limitedResults = $limitedResults;
		}
		protected function GetProdFromCollections_onlimitedDicsountPercentage() {
			if ( $this->conn ) {
				if ( $this->collection_name !== "" ) {
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->collection_name) && preg_match("/^[a-zA-Z0-9]+$/", $this->limitedResults) ) {
						/* SHOW TOP PRORDUCTS FOR LAST MONTH */
						$TodaysDate 			= date('Y-m-d');			// todays date
						$Last5Years				=  date('Y') - 5;			// last 5 Years
						$Last5Years 			= date("$Last5Years-m-d"); 	// last 5 years date

						$resultsLimit = $this->limitedResults;
						$collection_name 	= preg_replace("#[^a-zA-Z0-9]#", "", $this->collection_name);
						$collection_name 	= mysqli_real_escape_string($this->conn, $collection_name);
						$resultsLimit		= mysqli_real_escape_string($this->conn, $resultsLimit);

						if ( $GetProdFromCollections = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? AND prod_tags LIKE '%$collection_name%' ORDER BY RAND()") ) {
							$GetProdFromCollections->bind_param('ss', $Last5Years, $TodaysDate);
							$GetProdFromCollections->execute();
							$GetProdFromCollections->store_result();

							if ( $GetProdFromCollections->num_rows > 0 ) {
								$GetProdFromCollections->bind_result($prod_ref);
								$GetProdFromCollections->fetch();

								$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);			// products reference

								// get ranmdom 50 results from store
								$sql_check_val_query_re = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products ORDER BY RAND() LIMIT 50";

								$qry_results = mysqli_query($this->conn, $sql_check_val_query_re);
								if ( $qry_results ) {
									if ( mysqli_num_rows($qry_results) > 0 ) {
										$results_count = 0;
										while ( $row = mysqli_fetch_assoc($qry_results) ) {
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
									else {
										echo "No Records Found";
									}
								}
								else {
									echo "An Error Occured" . mysqli_error($conn);
								}
							}
							$GetProdFromCollections->close();		// close conenctions
						}
					}
					else {
						echo "Invalid Collection Name And/Or Results Limits Entered";
					}
				}
				else {
					die("Collections Name Cannot Be Empty");
				}


			}
		}

		public function show_outputs_results() {
			$this->GetProdFromCollections_onlimitedDicsountPercentage();
			if ( count($this->output_messages > 0) ) {
				return $this->output_messages;
			}
			else {
				echo "\nNo Output Results!!!";
			}
		}
	}


 ?>