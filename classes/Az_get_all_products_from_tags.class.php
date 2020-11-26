
<?php 
	// get all products from store using products tags and limited no of discounts
	class Az_get_all_products_from_tags {
		protected $conn;						// connections
		protected $output_messages;				// output messages
		protected $TagsView;					// tage name to search
		protected $discount_percentage_limits;	// percentage limits of discount to show
		protected $limitsResults;				// limited results

		public function __construct($conn, $TagsView, $discount_percentage_limits = 100, $limitsResults = 6) {
			$this->conn 						= $conn;							// connection
			$this->output_messages 				= $output_messages = [];			// output messages
			$this->TagsView 					= $TagsView;						// tags to show
			$this->discount_percentage_limits 	= $discount_percentage_limits;		// discount percentage limits
		}

		/**
		 * GET ALL PRODUCTS FROM MATCHED PRODUCTS TAGS
		 */
		protected function GetProductsFromTagsViewWithLimitedDiscount() {
			if ( $this->conn !== false ) {
				if ( $this->TagsView !== "" && $this->limitsResults !== "" ) {
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->TagsView) && preg_match("/^[a-zA-Z0-9]+$/", $this->discount_percentage_limits) ) {
						/* SHOW TOP PRORDUCTS FOR LAST MONTH */
						$TodaysDate 			= date('Y-m-d');			// todays date
						$Last5Years				=  date('Y') - 5;			// last 5 Years
						$Last5Years 			= date("$Last5Years-m-d"); 	// last 5 years date

						$limitsResults 	= $this->limitsResults;		// limited results
						$TagsView 							= preg_replace("#[^a-zA-Z0-9]#", "", $this->TagsView);
						$discount_percentage_limits 		= preg_replace("#[^a-zA-Z0-9]#", "", $this->discount_percentage_limits);
						$TagsView 							= mysqli_real_escape_string($this->conn, $TagsView);
						$discount_percentage_limits 		= mysqli_real_escape_string($this->conn, $discount_percentage_limits);
						$limitsResults						= mysqli_real_escape_string($this->conn, $limitsResults);

						$TodaysDate = mysqli_real_escape_string($this->conn, $TodaysDate);
						$Last5Years = mysqli_real_escape_string($this->conn, $Last5Years);


						if ( $GetProdFromCollections = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? AND discount_percentage BETWEEN 0 AND ? AND prod_tags LIKE '%{$TagsView}%' ORDER BY RAND() DESC LIMIT 4") ) {
							$GetProdFromCollections->bind_param('sss', $Last5Years, $TodaysDate, $discount_percentage_limits);
							$GetProdFromCollections->execute();
							$GetProdFromCollections->store_result();

							// check if any results exists in store
							if ( $GetProdFromCollections->num_rows > 0 ) {
								$GetProdFromCollections->bind_result($prod_ref);	// bind results
								$GetProdFromCollections->fetch();   				// fetch all results

								$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);			// products reference

								// if cat is greated than 12 show one product per cat
								$sql_check_val_query_re = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products WHERE prod_upload_date BETWEEN '$Last5Years' AND '$TodaysDate' AND discount_percentage BETWEEN 0 AND '$discount_percentage_limits' AND prod_tags LIKE '%$TagsView%' ORDER BY RAND() LIMIT 4";

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
									else {
										echo "No Records Found";
									}
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
			else {
				die("Connection Error Please Try Again Later");
			}
		}



		/**
		 * Show all outputs messages
		 * @return [string] [output messages]
		 */
		public function show_outputs_results() {
			$this->GetProductsFromTagsViewWithLimitedDiscount();
			if ( count($this->output_messages > 0) ) {
				return $this->output_messages;
			}
			else {
				echo "\nNo Results Found!!!";
			}
		}
	}


 ?>