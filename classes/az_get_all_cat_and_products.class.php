<?php 
	/**
	 * GEL ALL PRODUCTS AND ALL CATEGORIES FROM STORE.. 
	 * BASED ON ORDER TYPE
	 * 			CAT NAME,
	 * 			PRODUCTS REF,
	 * 			and
	 * 			PRODUCTS TITLE
	 */
	class Az_get_all_cat_and_products {
		protected $conn;							// Connection
		protected $products_ref;					// Products Ref
		protected $products_title;					// Posts And/Or Products Title
		public $all_error;							// All Error Messages Holder
		protected $output_messages = [];			// All Output Messages Holder
		protected $orderType;						// Order Type
		protected $catName;							// Category Name
		protected $resultsLimit;					// results limits

		public function __construct($conn, $products_ref, $products_title = "", $orderType = "", $catName = "", $resultsLimit = 1, $all_error = "") {
			$this->conn 				= $conn;								// Connection
			$this->products_ref 		= $products_ref;						// Products Ref
			$this->products_title 		= $products_title;						// Posts Title And/Or Products Title
			$this->orderType 			= $orderType;							// Order Type
			$this->catName 				= $catName;								// Category Name
			$this->resultsLimit 		= $resultsLimit;						// results limits
			$this->all_error 			= $all_error;							// All Error Message Holder
			$this->output_messages 		= $output_messages = [];				// Output Messages Holder

			$this->orderType 			= strtolower($this->orderType);	
			$this->orderType 			= ucfirst($this->orderType);
		}

		/**
		 * check if products exists in STORE using prod ref
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
		public function CheckProducts() {
			return $this->ProductsCheck();
		}

		/**
		 * GET PRODUCTS CATEGORIES
		 */
		protected function Get_products_categories() {
			if ( $this->products_ref !== "" ) {
				// get products categories
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->products_ref);
					if ( $GtCatFrmProTit = $this->conn->prepare("SELECT prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {

						$GtCatFrmProTit->bind_param("s", $this->products_ref);
						$GtCatFrmProTit->execute();
						$GtCatFrmProTit->store_result();

						if ( $GtCatFrmProTit->num_rows > 0 ) {
							$GtCatFrmProTit->bind_result($prod_category);
							$GtCatFrmProTit->fetch();
							$prod_category = mysqli_real_escape_string($this->conn, $prod_category);
							$this->output_messages[] = $prod_category;

						}
						else {
							$this->all_error = "No Categories Found";
						}
						$GtCatFrmProTit->close();
					}
				}
				else {
					$this->all_error = "Invalid Products Title";
				}
			}
			else {
				$this->all_error = "Products Title cannot Be Empty";
			}
		}



		/**
		 * GET RELATED PRODUCTS FROM POSTS TITLE
		 */
		protected function Get_related_products_from_title() {
			// show related products from posts title
			if ( $this->products_ref !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->products_ref);
					if ( $GtPostsTitle = $this->conn->prepare("SELECT post_title, prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {

						$GtPostsTitle->bind_param("s", $this->products_ref);
						$GtPostsTitle->execute();
						$GtPostsTitle->store_result();

						if ( $GtPostsTitle->num_rows > 0 ) {
							$GtPostsTitle->bind_result($post_title, $prod_category);
							$GtPostsTitle->fetch();
							$post_title = mysqli_real_escape_string($this->conn, $post_title);
							

							// select all products from store using posts title match
							if ( $GtPostTitleMatch = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE post_title LIKE '%$post_title%' OR prod_category = ? ORDER BY id ASC LIMIT 1 ") ) { 
								$GtPostTitleMatch->bind_param("s", $prod_category);
								$GtPostTitleMatch->execute();
								$GtPostTitleMatch->store_result();
								if ( $GtPostTitleMatch->num_rows > 0 ) {
									$GtPostTitleMatch->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
									$GtPostTitleMatch->fetch(); // fetch all results

									$resultsLimit = $this->resultsLimit;
									$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $resultsLimit);
									$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);
									$sql_get_posts_title_match = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage
										FROM az_ldm__products 
										WHERE post_title LIKE '%$post_title%' OR prod_category = '$prod_category' 
										ORDER BY RAND() LIMIT $resultsLimit";

									$qry_pst_title = mysqli_query($this->conn, $sql_get_posts_title_match);
									if ( $qry_pst_title ) {
										if ( mysqli_num_rows($qry_pst_title) > 0 ) {
											$results_count = 0;
											while ($row = mysqli_fetch_assoc($qry_pst_title)) {
												$results_count++;
												if ( $results_count <= ($resultsLimit ) ) {
													$row['pics_up_names'] 		= unserialize($row['pics_up_names']);
													$row['pics_up_real_name'] 	= unserialize($row['pics_up_real_name']);
													if ( is_array($row['pics_up_names']) && is_array($row['pics_up_real_name']) ) {
														// show tyhe first pics 
														$pics_up_names = $row['pics_up_names'][0]; 
														$pics_real_name = $row['pics_up_real_name'][0];
													}
													else {
														$pics_up_names = $row['pics_up_names'];
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
													$this->output_messages[] = $row;		// push all results to output messages array holder
												}
											}
										}
									}
								}
								$GtPostTitleMatch->close(); // close connections
							}

						}
						else {
							$this->all_error = "No Categories Found";
						}
						$GtPostsTitle->close();
					}
				}
				else {
					$this->all_error = "Invalid Products Title";
				}
			}
			else {
				$this->all_error = "Products Title cannot Be Empty";
			}
		}


		/**
		 * GET RECENT PRODUCTS FROM CATAGORIES
		 */
		protected function Get_products_from_categories() {
			if ( $this->products_ref !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9, ]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9, ]#", "", $this->products_ref);

					if ( $GtCat = $this->conn->prepare("SELECT prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {
						$GtCat->bind_param("s", $this->products_ref);
						$GtCat->execute();
						$GtCat->store_result();
						if ( $GtCat->num_rows > 0 ) {
							$GtCat->bind_result($prod_category);
							$GtCat->fetch();
							$prod_category = mysqli_real_escape_string($this->conn, $prod_category);
							if ( $this->catName !== "" ) {
								$this->catName = mysqli_real_escape_string($this->conn, $this->catName);
								$prod_category = $this->catName;
							}

							$resultsLimit = $this->resultsLimit;
							$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $resultsLimit);
							$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);

							// SELECT RANDOM TOP 50 RESULTS USING CAT MATCH
							$sql_select_pro_frm_cat = 
								"SELECT pics_up_names, 
										pics_up_real_name, 
										post_title, 
										prod_ref, 
										prod_prices, 
										discount_price, 
										discount_percentage 
								FROM az_ldm__products WHERE prod_category = '$prod_category' 
								ORDER BY RAND() LIMIT 50";


							$qry_select_pro_frm_cat = mysqli_query($this->conn, $sql_select_pro_frm_cat);			// query all results
							if ( $qry_select_pro_frm_cat ) {
								$count_rows = mysqli_num_rows($qry_select_pro_frm_cat);		// no of rows of reults gotten
								$results_count = 0;
								while ( $row = mysqli_fetch_assoc($qry_select_pro_frm_cat) ) {
									$results_count++;
									if ( $results_count <= ( $resultsLimit ) ) {
										$row['pics_up_names'] 		= unserialize($row['pics_up_names']);
										$row['pics_up_real_name'] 	= unserialize($row['pics_up_real_name']);
										if ( is_array($row['pics_up_names']) && is_array($row['pics_up_real_name']) ) {
											// show tyhe first pics 
											$pics_up_names = $row['pics_up_names'][0]; 
											$pics_real_name = $row['pics_up_real_name'][0];
										}
										else {
											$pics_up_names = $row['pics_up_names'];
											$pics_real_name = $row['pics_up_real_name'];
										}
										// get image type from real image pics name
										$imageType = explode(".", $pics_real_name);
										$imageType = array_pop($imageType);	// get image type from image name extension

									
										$row['pics_up_names'] 		= mysqli_real_escape_string($this->conn, $pics_up_names);			// pics tmp name
										$row['pics_up_real_name'] 	= mysqli_real_escape_string($this->conn, $pics_real_name);			// pics real name
										$row['post_title'] 			= mysqli_real_escape_string($this->conn, $row['post_title']);		// posts title
										$row['prod_ref'] 			= mysqli_real_escape_string($this->conn, $row['prod_ref']);			// prod ref
										$row['imageType'] 			= mysqli_real_escape_string($this->conn, $imageType);				// image type
										$row['prod_prices'] 		= mysqli_real_escape_string($this->conn, $row['prod_prices']);		// products prices
										$row['discount_price'] 		= mysqli_real_escape_string($this->conn, $row['discount_price']);	// dicsount price
										$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage


										$this->output_messages[] = $row;		// push all results to output messages array holder
									}
								}
							} 
						}
						$GtCat->close(); // close conenction
					}
				} 
			}
		}

		/**
		 * [Get_top_viewed_products GET RANDOM TOP VIEWED PRODUCTS]
		 */
		protected function Get_top_viewed_products() {
			//SHOW TOP VIEWD PRODUCTS
			if ( $this->orderType !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
					$this->orderType = preg_replace("#[^a-zA-Z0-9]#", "", $this->orderType);
					if ( isset($this->conn) ) {
						$this->orderType = mysqli_real_escape_string($this->conn, $this->orderType);
						if ( $this->orderType === "Mostviewedcategory" ) {
							// show top 10 most viewed products
							$showTopViewCat = true;
						}
						else {
							// show most viewed category to 10
							$showTopViewCat = false;
						}

						/**
						 * SHOW TOP PRORDUCTS FOR LAST 5 YEARS IN THIS CASE
						 */
						$TodaysDate 			= date('Y-m-d');				// todays date
						$ThisYear 				= date('Y');					// this year date i.e 2020
						$LastFiveYears 			= date('Y') - 5;				// last five years date i.e 2020 - 5

						$LastFiveYearsFullDate 	= date("$LastFiveYears-m-d"); 	// last five years date

						// get results limits
						$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $this->resultsLimit);	// results limit
						$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);

						if ( $GetTopViewdPro = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? ORDER BY id DESC LIMIT 10") ) {
							$GetTopViewdPro->bind_param('ss', $LastFiveYearsFullDate, $TodaysDate);
							$GetTopViewdPro->execute();
							$GetTopViewdPro->store_result();

							if ( $GetTopViewdPro->num_rows > 0 ) {
								$GetTopViewdPro->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
								$GetTopViewdPro->fetch();

								if ( $showTopViewCat === true ) {									
									// check if any products exists in products logs
									// this case we use category name to select top viewed products
									$sql_Ch_prod_query = 
										"SELECT category_name 
										FROM az_ldm__products_logs WHERE prod_ref IS NOT NULL 
										ORDER BY RAND() LIMIT $resultsLimit";
								}
								else {
									// check if any products exists in products logs
									// this case we use products ref to display top viewed products
									$sql_Ch_prod_query = 
										"SELECT prod_ref 
										FROM az_ldm__products_logs WHERE prod_ref IS NOT NULL 
										ORDER BY RAND() LIMIT $resultsLimit";
								}

								$sql_Ch_prod = mysqli_query($this->conn, $sql_Ch_prod_query);		// query results

								// check if any results is found in store
								if ( mysqli_num_rows($sql_Ch_prod) > 0 ) {
									// get all rsults in store
									while ($row = mysqli_fetch_assoc($sql_Ch_prod)) {
										if ( $showTopViewCat === true ) {
											// select top viwed categories and display random products
											$checkVal = $row['category_name'];		// category name

											// get all products with same category name  with $checkVal
											$sql_check_val_query = 
												"SELECT pics_up_names, 
														pics_up_real_name, 
														post_title, 
														prod_ref, 
														prod_prices, 
														discount_price, 
														discount_percentage 
												FROM az_ldm__products WHERE prod_upload_date 
												BETWEEN '$LastFiveYearsFullDate' AND '$TodaysDate' AND prod_category = '$checkVal' 
												ORDER BY RAND() DESC LIMIT 1";
										}
										else {
											// select most viwed products from
											$checkVal = $row['prod_ref'];
											$sql_check_val_query = 
												"SELECT pics_up_names, 
														pics_up_real_name, 
														post_title, 
														prod_ref, 
														prod_prices, 
														discount_price, 
														discount_percentage 
												FROM az_ldm__products WHERE prod_upload_date 
												BETWEEN '$LastFiveYearsFullDate' AND '$TodaysDate' AND prod_ref = '$checkVal' 
												ORDER BY RAND() ASC LIMIT 1";
										}
										$qry_sql_check_val_query = mysqli_query($this->conn, $sql_check_val_query);

										// chesk if reuslts is found in store
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
													$this->output_messages[] = $row;		// push all results to array holder
												}
											}
										}
									}
								}
							}
							
							$GetTopViewdPro->close();		// close all connections
						}
					}
				}
			}
		}

		/**
		 * GET ONE PRODUCTS AS FEATURED CATEGORIES FROM NEWLY CREATED CATEGORIES
		 */
		protected function Get_prod_from_newCreated_cat() {
			if ( $this->orderType !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
					$this->orderType = preg_replace("#[^a-zA-Z0-9]#", "", $this->orderType);
					if ( isset($this->conn) ) {
						$this->orderType = mysqli_real_escape_string($this->conn, $this->orderType);
						if ( $this->orderType === "Featuredcategory" ) {
							/**
							 * SHOW TOP PRORDUCTS FOR LAST 5 YEARS IN THIS CASE
							 */
							$TodaysDate 			= date('Y-m-d');				// todays date
							$ThisYear 				= date('Y');					// this year date i.e 2020
							$LastFiveYears 			= date('Y') - 5;				// last five years date i.e 2020 - 5

							$LastFiveYearsFullDate 	= date("$LastFiveYears-m-d"); 	// last five years date

							// get results limits
							$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $this->resultsLimit);	// results limit
							$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);

							if ( $GetTopViewdPro = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? ORDER BY id DESC LIMIT 1") ) {
								$GetTopViewdPro->bind_param('ss', $LastFiveYearsFullDate, $TodaysDate);
								$GetTopViewdPro->execute();
								$GetTopViewdPro->store_result();

								if ( $GetTopViewdPro->num_rows > 0 ) {
									$GetTopViewdPro->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
									$GetTopViewdPro->fetch();

									// get all categories name from products in products logs
									$sql_Ch_prod_query = 
										"SELECT DISTINCT(category_name) 
											FROM az_ldm__products_logs 
											WHERE prod_ref IS NOT NULL 
										ORDER BY RAND() AND prod_views DESC LIMIT $resultsLimit";
									$sql_Ch_prod = mysqli_query($this->conn, $sql_Ch_prod_query);

									// if cat is greated than 12 show one product per cat
									if ( mysqli_num_rows($sql_Ch_prod) > 0) {
										while ($row = mysqli_fetch_assoc($sql_Ch_prod)) {
											// show products by category name that has been viewed by someone 
											$checkVal = $row['category_name'];		// category name

											// get matched category from category name
											$sql_check_val_query = 
												"SELECT pics_up_names, 
														pics_up_real_name, 
														post_title, 
														prod_category, 
														prod_ref, 
														prod_prices, 
														discount_price, 
														discount_percentage 
												FROM az_ldm__products
												WHERE prod_upload_date 
												BETWEEN '$LastFiveYearsFullDate' AND '$TodaysDate' AND prod_category = '$checkVal' 
												ORDER BY RAND() ASC LIMIT 1";
											$qry_sql_check_val_query = mysqli_query($this->conn, $sql_check_val_query);

											// check if any results exists in store
											if ( mysqli_num_rows($qry_sql_check_val_query) > 0 ) {
												$results_count = 0;
												// get all results
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
														$row['prod_category'] 		= mysqli_real_escape_string($this->conn, $row['prod_category']);	// Products Categories
														$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage
														$this->output_messages[] = $row;		// push all results to output messages array holder
													}
												}
											}
										}
									}
								}

								$GetTopViewdPro->close();
							}
						}
					}
				}
			}
		}

		/**
		 * SHOW RECENT PRODUCTS FROM THIS YEAR TO LAST 2 YEARS
		 */
		protected function Get_recent_products_from_date() {
			// SHOW RECENTLY ADDED PRODUCTS
			$thisYear 		= date("Y");						// this year
			$LastTwoYears 	= $thisYear-2;						// last 2 years
			$todayDate 		= date("Y-m-d");					// todays date
			$lastTwoYears 	= date("$LastTwoYears-m-d");		// last years date

			if ( $GtPostsTitle = $this->conn->prepare("SELECT post_title, prod_category FROM az_ldm__products WHERE prod_upload_date BETWEEN ? AND ? ORDER BY id DESC LIMIT 20") ) {

				$GtPostsTitle->bind_param("ss", $lastTwoYears, $todayDate);
				$GtPostsTitle->execute();			// execute query
				$GtPostsTitle->store_result();		// store results


				// check if any products exists in store
				if ( $GtPostsTitle->num_rows > 0 ) {
					$GtPostsTitle->bind_result($post_title, $prod_category);		// bnd results
					$GtPostsTitle->fetch();											// fetch all results
					$post_title = mysqli_real_escape_string($this->conn, $post_title);
					
					// get results limits
					$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $this->resultsLimit);	// results limit
					$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);		// escaped results limits

					// check if any products exists last 2 years
					if ( $GtProdRecent = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_upload_date BETWEEN '$lastTwoYears' AND '$todayDate' ORDER BY id DESC LIMIT 1") ) { 
						$GtProdRecent->execute();
						$GtProdRecent->store_result();
						if ( $GtProdRecent->num_rows > 0 ) {
							$GtProdRecent->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
							$GtProdRecent->fetch(); 
							if ( isset($useCatSelect) && $useCatSelect = true ) {
								# SHOW ALL PRODUCTS FROM STORE USING DATE DIFFERENCE AND PRODUCTS CATEGORIES
								$sql_get_posts_title_match = 
									"SELECT pics_up_names, 
											pics_up_real_name, 
											post_title, 
											prod_ref, 
											prod_prices, 
											discount_price, 
											discount_percentage
									FROM az_ldm__products 
									WHERE prod_upload_date BETWEEN '$lastTwoYears' AND '$todayDate' AND prod_category = '$prod_category' 
									ORDER BY id DESC LIMIT 50";
							}
							else {
								# SHOW ALL PRODUCTS USING DATE DIFFERENCE ONLY
								$sql_get_posts_title_match = 
									"SELECT pics_up_names, 
											pics_up_real_name, 
											post_title, 
											prod_ref, 
											prod_prices, 
											discount_price, 
											discount_percentage
									FROM az_ldm__products 
									WHERE prod_upload_date BETWEEN '$lastTwoYears' AND '$todayDate'
									ORDER BY id DESC LIMIT 50";
								}

							$qry_pst_title = mysqli_query($this->conn, $sql_get_posts_title_match);
							if ( $qry_pst_title ) {
								$results_count = 0;

								// check if results exists in store
								if ( mysqli_num_rows($qry_pst_title) > 0 ) {
									// fetch all results
									while ($row = mysqli_fetch_assoc($qry_pst_title)) {
										$results_count++;
										if ( $results_count <= ( $resultsLimit ) ) {
											$row['pics_up_names'] 		= unserialize($row['pics_up_names']);		// pics names
											$row['pics_up_real_name'] 	= unserialize($row['pics_up_real_name']);

											// check ia pics real name and tmp name is an array in store
											if ( is_array($row['pics_up_names']) && is_array($row['pics_up_real_name']) ) {
												// show tyhe first pics 
												$pics_up_names = $row['pics_up_names'][0]; 
												$pics_real_name = $row['pics_up_real_name'][0];
											}
											else {
												$pics_up_names = $row['pics_up_names'];				// pics real names
												$pics_real_name = $row['pics_up_real_name'];		// pics real names
											}
											// get image type from real image pics name
											$imageType = explode(".", $pics_real_name);		// exploded image type from image name
											$imageType = array_pop($imageType);				// real image type

											$row['pics_up_names'] 		= mysqli_real_escape_string($this->conn, $pics_up_names);				// pics tmp name
											$row['pics_up_real_name'] 	= mysqli_real_escape_string($this->conn, $pics_real_name);				// pics real name
											$row['post_title'] 			= mysqli_real_escape_string($this->conn, $row['post_title']);			// posts title
											$row['prod_ref'] 			= mysqli_real_escape_string($this->conn, $row['prod_ref']);				// prod ref
											$row['imageType'] 			= mysqli_real_escape_string($this->conn, $imageType);					// image type
											$row['prod_prices'] 		= mysqli_real_escape_string($this->conn, $row['prod_prices']);			// products prices
											$row['discount_price'] 		= mysqli_real_escape_string($this->conn, $row['discount_price']);		// dicsount price
											$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage
											$this->output_messages[] = $row;			// push all results to output messagea array holder
										}
									}
								}
							}
						}
						$GtProdRecent->close(); // close connections
					}

				}
				else {
					$this->all_error = "No Categories Found";
				}
				$GtPostsTitle->close();			// close all connections
			}
		}

		// GET PRODUCTS FROM PRODUCTS REFERENCE 
		// ONLY ONE PRODUCTS IS EXPECTED AS RESULTS
		protected function Get_products_from_ref() {
			// show related products from products ref
			if ( $this->products_ref !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->products_ref);
					if ( $GtPostsTitle = $this->conn->prepare("SELECT post_title, prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {

						$GtPostsTitle->bind_param("s", $this->products_ref);
						$GtPostsTitle->execute();
						$GtPostsTitle->store_result();

						if ( $GtPostsTitle->num_rows > 0 ) {
							$GtPostsTitle->bind_result($post_title, $prod_category);
							$GtPostsTitle->fetch();
							$prod_category 	= mysqli_real_escape_string($this->conn, $prod_category);
							

							// select all products from store using posts ref
							if ( $GtPostTitleMatch = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_ref = ? AND prod_category = '{$prod_category}' ORDER BY id DESC LIMIT 1 ") ) { 
								$GtPostTitleMatch->bind_param("s", $this->products_ref);
								$GtPostTitleMatch->execute();
								$GtPostTitleMatch->store_result();
								if ( $GtPostTitleMatch->num_rows > 0 ) {
									$GtPostTitleMatch->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
									$GtPostTitleMatch->fetch(); // fetch all results
									$sql_get_posts_title_match = 
										"SELECT pics_up_names, 
												pics_up_real_name, 
												post_title, 
												prod_ref, 
												prod_prices, 
												prod_color, 
												prod_size, prod_description 
										FROM az_ldm__products 
										WHERE prod_ref = '{$prod_ref}' AND prod_category = '$prod_category' 
										ORDER BY id DESC LIMIT 1";

									$qry_pst_title = mysqli_query($this->conn, $sql_get_posts_title_match);
									if ( $qry_pst_title ) {
										if ( mysqli_num_rows($qry_pst_title) > 0 ) {
											while ($row = mysqli_fetch_assoc($qry_pst_title)) {
												$row['pics_up_names'] = unserialize($row['pics_up_names']);
												$row['pics_up_real_name'] = unserialize($row['pics_up_real_name']);
		
												$pics_up_names = $row['pics_up_names'];
												$pics_real_name = $row['pics_up_real_name'];
												// get image type from real image pics name

												$row['post_title'] 			= mysqli_real_escape_string($this->conn, $row['post_title']);		// posts title
												$row['prod_ref'] 			= mysqli_real_escape_string($this->conn, $row['prod_ref']);			// products ref
												$row['prod_prices'] 		= mysqli_real_escape_string($this->conn, $row['prod_prices']);		// products prices
												// $row['prod_color'] 			= mysqli_real_escape_string($this->conn, $row['prod_color']);		// products colors
												// $row['prod_size'] 			= mysqli_real_escape_string($this->conn, $row['prod_size']);		// products sizes

												array_push($this->output_messages, $row);		// push all results to output message array holder
											}
										}
									}
								}
								$GtPostTitleMatch->close(); // close connections
							}

						}
						else {
							$this->all_error = "No Categories Found";
						}
						$GtPostsTitle->close();
					}
				}
				else {
					$this->all_error = "Invalid Products Title";
				}
			}
			else {
				$this->all_error = "Products Title cannot Be Empty";
			}
		}


		// GET RECENT VIEWED PRODUCTS FROM PRODUCTS REF
		protected function Get_recent_viewed_products() {
			// show related products from products ref
			if ( $this->products_ref !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->products_ref) ) {
					$this->products_ref = preg_replace("#[^a-zA-Z0-9]#", "", $this->products_ref);
					if ( $GtPostsTitle = $this->conn->prepare("SELECT post_title, prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {
						// bind parameters
						$GtPostsTitle->bind_param("s", $this->products_ref);
						$GtPostsTitle->execute();		// execute query
						$GtPostsTitle->store_result();	// store results

						// check if records exists
						if ( $GtPostsTitle->num_rows > 0 ) {
							$GtPostsTitle->bind_result($post_title, $prod_category);					// bind results
							$GtPostsTitle->fetch();														// fetch results
							$prod_category 	= mysqli_real_escape_string($this->conn, $prod_category);	// products category
							
							// get results limits
							$resultsLimit = preg_replace("#[^a-zA-Z0-9]#", "", $this->resultsLimit);	// results limit
							$resultsLimit = mysqli_real_escape_string($this->conn, $resultsLimit);		// escaped results limits

							// select all products from store using posts ref
							if ( $GetPostsRefMatch = $this->conn->prepare("SELECT pics_up_names, pics_up_real_name, post_title, prod_ref FROM az_ldm__products WHERE prod_ref = ? AND prod_category = '{$prod_category}' ORDER BY id DESC LIMIT 1 ") ) { 
								$GetPostsRefMatch->bind_param("s", $this->products_ref);
								$GetPostsRefMatch->execute();
								$GetPostsRefMatch->store_result();
								if ( $GetPostsRefMatch->num_rows > 0 ) {
									$GetPostsRefMatch->bind_result($pics_up_names, $pics_up_real_name, $post_title, $prod_ref);
									$GetPostsRefMatch->fetch(); // fetch all results
									$sql_get_posts_title_match = 
										"SELECT pics_up_names, 
												pics_up_real_name, 
												post_title, 
												prod_category, 
												prod_ref, 
												prod_prices, 
												discount_price, 
												discount_percentage 
										FROM az_ldm__products
										WHERE prod_ref = '{$prod_ref}' AND prod_category = '$prod_category' 
										ORDER BY id DESC LIMIT 1";

									$qry_sql_check_val_query = mysqli_query($this->conn, $sql_get_posts_title_match);
									if ( $qry_sql_check_val_query ) {
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
													$row['prod_category'] 		= mysqli_real_escape_string($this->conn, $row['prod_category']);	// Products Categories
													$row['discount_percentage'] = mysqli_real_escape_string($this->conn, $row['discount_percentage']);	// discount products percentage
													$this->output_messages[] = $row;		// output messages
												}
											}
										}
									}
								}
								$GetPostsRefMatch->close(); // close connections
							}

						}
						else {
							$this->all_error = "No Categories Found";
						}
						$GtPostsTitle->close();		// close all conenctions
					}
				}
				else {
					$this->all_error = "Invalid Products Title";
				}
			}
			else {
				$this->all_error = "Products Title cannot Be Empty";
			}
		}


		/**
		 * SWITCH ORDER TYPE TO DISPLAY PRODUCTS FROM ORDER TYPE VALUE
		 * @return [type] [call back function]
		 */
		protected function switch_order_type() {
			// SWITCH BETWEEN ORDER TYPE
			switch ($this->orderType) {
				case "Title":
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) &&  preg_match("/^[a-zA-Z0-9, ]+$/", $this->products_title) ) {
						$this->Get_related_products_from_title(); // show recent products from title
					}
				break;
				case "Categories":
					// select products from categories
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
					 	$this->Get_products_from_categories();
					}
				break;
				case  "Mostviewedcategory":
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						 $this->Get_top_viewed_products();
					}
				break;
				case  "Mostviewedproducts":
					// select most viewed products
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						 $this->Get_top_viewed_products();
					}
				break;
				case  "Recent":
					// show recent products 
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						// show recent products using one year interval
						 $this->Get_recent_products_from_date();
					}
				break;
				case  "Recentviewed":
					// show recent products 
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						// show recent products using one year interval
						 $this->Get_recent_viewed_products();
					}
				break;
				case "Products":
					// show recent products 
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						// show recent products using one year interval
						 $this->Get_products_from_ref();
					}
				break;
				case "Featuredcategory":
					// show featured categories products 
					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
						 $this->Get_prod_from_newCreated_cat();
					}
				break;
				default:
					echo "Order Type Not Found" . "<br>";
					echo $this->orderType;
				break;
			}		
		}
	
		
		/**
		 * SHOW ALL OUPUTS RESULTS
		 * @return [array] [OUTPUT MESSAGES OR RESULTS]
		 */
		public function show_outputs() {
			// check if no error is found
			$this->switch_order_type();
			if ( $this->all_error === "" ) {
				if ( is_array($this->output_messages) && ( count($this->output_messages) > 0 ) ) {
					// show all results
					return $this->output_messages; // show all results
				}
			}
		}


		/**
		 * GET ORDER TYPE NAME
		 * @return [type] [order type]
		 */
		public function getOrderType() {
			if ( $this->orderType !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9]+$/", $this->orderType) ) {
					$this->orderType = preg_replace("#[^a-zA-Z0-9]#", "", $this->orderType);
					return $this->orderType;
				}
			}
		}

		/**
		 * CLOSE ALL CONNECTIONS
		 */
		protected function Close_all_conections() {
			if (isset($this->conn)) {
				mysqli_close($this->conn); // close all connections
			}
		}
	}

?>
