<?php 
	
	// THIS SCRIPT CLASS FILE GETS PRODUCTS CATEGORY NAME
	
	class Az_get_products_categories {
		protected $conn;
		protected $category_name;
		protected $sub_cat_name;
		protected $output_messages;
		protected $output_sub_categories;
		public $output_prod_ref;
		protected $all_error_messages;

		public function __construct($conn, $category_name, $sub_cat_name = "") {
			$this->conn 					= $conn;
			$this->category_name 			= $category_name;
			$this->sub_cat_name 			= $sub_cat_name;
			$this->output_messages 			= $output_messages = "";
			$this->output_sub_categories 	= $output_sub_categories = [];
			$this->output_prod_ref 			= $output_prod_ref = "";
			$this->all_error_messages 		= $all_error_messages = "";
		}

		protected function Check_if_categories_exists() {
			// we check if categories exists in store
			if ( $this->conn !== false ) {
				if ( $this->category_name !== "" ) {
					$this->category_name = htmlentities($this->category_name);
					$this->category_name = strip_tags($this->category_name);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->category_name) ) {
						$this->category_name = mysqli_real_escape_string($this->conn, $this->category_name);

						// check if category exists in store
						if ( $checkCat = $this->conn->prepare("SELECT prod_ref FROM az_ldm__products WHERE prod_category = ? ORDER BY id DESC LIMIT 1") ) {
							$checkCat->bind_param("s", $this->category_name);	// bind parameters
							$checkCat->execute();								// execute query
							$checkCat->store_result();							// store results

							if ( $checkCat->num_rows > 0 ) {
								// category exists
								$checkCat->bind_result($prod_ref);
								$checkCat->fetch();


								$this->prod_ref = $prod_ref;
								return true;
							}
							else {
								$this->all_error_messages = "Category {$this->category_name} Does Not Exists";
							}
							$checkCat->close();									// close connections
						}
					}
					else {
						$this->all_error_messages = "invalid Category Name Entered";
					}
				}
				else {
					$this->all_error_messages = "Category Name Cannot Be Empty";
				}
			}
		}

		protected function Check_if_any_products_exists_in_category() {
			if ( $this->conn !== false ) {
				if ( $this->category_name !== "" ) {
					$this->category_name = htmlentities($this->category_name);
					$this->category_name = strip_tags($this->category_name);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->category_name) ) {
						$this->category_name = preg_replace("#[^a-zA-Z0-9]#", "", $this->category_name);

						$this->category_name = mysqli_real_escape_string($this->conn, $this->category_name);

						// check if products exists in carts
						if ( $stmt = $this->conn->prepare("SELECT prod_category, prod_ref FROM az_ldm__products WHERE prod_category = ? ORDER BY id DESC LIMIT 1") ) {
							$stmt->bind_param("s", $this->category_name);
							$stmt->execute();
							$stmt->store_result();

							if ( $stmt->num_rows > 0 ) {
								// products exists
								$stmt->bind_result($prod_category, $prod_ref);
								$stmt->fetch();

								$prod_category 		= mysqli_real_escape_string($this->conn, $prod_category);
								$prod_ref 			= mysqli_real_escape_string($this->conn, $prod_ref);

								if ( preg_match("/^[a-zA-Z0-9]+$/", $prod_ref) ) {
									$this->output_prod_ref = $prod_ref;
									return true;
								}
								
							}
							else {
								$this->all_error_messages = "No products Found With Products Ref Of {$this->prod_ref}";
							}
						}
						else {
							$this->all_error_messages = msyqli_error($this->conn);
						}
					}
					else {
						$this->all_error_messages = "invalid Products Ref Entered";
					}
				}
				else {
					$this->all_error_messages = "Products Ref Cannot Be Empty";
				}
			}
		}

		protected function Get_all_products_in_category() {
			// get all products from categories in store
			if ( $this->conn !== false ) {
				if ( $stmt = $this->conn->prepare("SELECT prod_category FROM az_ldm__products WHERE prod_ref = ? ORDER BY id DESC LIMIT 1") ) {
					$stmt->bind_param("s", $this->prod_ref);
					$stmt->execute();
					$stmt->store_result();

					if ( $stmt->num_rows > 0 ) {
						// products exists
						$stmt->bind_result($prod_category);
						$stmt->fetch();

						$resultsLimits = 20;
						// get ranmdom 20 results from store
						$sql_check_val_query_re = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products ORDER BY RAND() LIMIT {$resultsLimits}";

						$qry_results = mysqli_query($this->conn, $sql_check_val_query_re);
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
					}
					else {
						$this->all_error_messages = "No products Found With Products Ref Of {$this->prod_ref}";
					}
				}
			}
		}

		/**
		 * THIS PROTECTED FUNCTION GET ALL SUBCATEGORIES FROM STORE
		 */
		protected function Get_all_sub_categories() {
			if ( $this->conn !== false ) {
				if ( $this->category_name !== "" ) {
					$this->category_name = htmlentities($this->category_name);
					$this->category_name = strip_tags($this->category_name);

					if ( preg_match("/^[a-zA-Z0-9]+$/", $this->category_name) ) {
						$this->category_name = preg_replace("#[^a-zA-Z0-9]#", "", $this->category_name);

						$this->category_name = mysqli_real_escape_string($this->conn, $this->category_name);

						// check if products exists in carts
						if ( $stmt = $this->conn->prepare("SELECT prod_category FROM az_ldm__products WHERE prod_category = ? ORDER BY id DESC LIMIT 1") ) {
							$stmt->bind_param("s", $this->category_name);
							$stmt->execute();
							$stmt->store_result();

							if ( $stmt->num_rows > 0 ) {
								// products exists
								$stmt->bind_result($prod_category);
								$stmt->fetch();

								$prod_category 		= mysqli_real_escape_string($this->conn, $prod_category);

								// select all 10 random products sub categories
								$sql_select_sub_categories = "SELECT DISTINCT prod_sub_category FROM az_ldm__products WHERE prod_category = '$prod_category' ORDER BY RAND() LIMIT 5";
								$qry_select_sub_categories = mysqli_query($this->conn, $sql_select_sub_categories);

								if ( $qry_select_sub_categories ) {
									if ( mysqli_num_rows($qry_select_sub_categories) > 0 ) {
										// products sub categories is found
										while ($row = mysqli_fetch_assoc($qry_select_sub_categories)) {
										    $this->output_sub_categories[] = $row;
										}
										return true;
									}
								}
								else {
									echo mysqli_error($this->conn);
								}
							}
							else {
								$this->all_error_messages = "No products Found With Products Ref Of {$this->prod_ref}";
							}
						}
						else {
							$this->all_error_messages = msyqli_error($this->conn);
						}
					}
					else {
						$this->all_error_messages = "Invalid Category Name Entered";
					}
				}
				else {
					$this->all_error_messages = "Category Name Cannot Be Empty";
				}
			}
		}


		protected function Get_all_products_from_sub_cat() {
			if ( $this->conn !== false ) {
				if ( $this->category_name !== "" && $this->sub_cat_name !== "" ) {
					$this->category_name = htmlentities($this->category_name);
					$this->category_name = strip_tags($this->category_name);

					$this->sub_cat_name = htmlentities($this->sub_cat_name);
					$this->sub_cat_name = strip_tags($this->sub_cat_name);

					if ( preg_match("/^[a-zA-Z0-9 ]+$/", $this->category_name) && preg_match("/^[a-zA-Z0-9 ]+$/", $this->sub_cat_name) ) {
						$this->category_name 		= preg_replace("#[^a-zA-Z0-9 ]#", "", $this->category_name);
						$this->sub_cat_name 		= preg_replace("#[^a-zA-Z0-9 ]#", "", $this->sub_cat_name);
						$this->category_name 		= mysqli_real_escape_string($this->conn, $this->category_name);
						$this->sub_cat_name 		= mysqli_real_escape_string($this->conn, $this->sub_cat_name);

						// check if products exists in carts
						if ( $stmt = $this->conn->prepare("SELECT prod_category, prod_sub_category FROM az_ldm__products WHERE prod_sub_category = ? AND prod_category = ? ORDER BY id DESC LIMIT 1") ) {
							$stmt->bind_param("ss", $this->sub_cat_name, $this->category_name);
							$stmt->execute();
							$stmt->store_result();

							if ( $stmt->num_rows > 0 ) {
								// products exists
								$stmt->bind_result($prod_category, $prod_sub_category);
								$stmt->fetch();

								$prod_category 			= mysqli_real_escape_string($this->conn, $prod_category);
								$prod_sub_category 		= mysqli_real_escape_string($this->conn, $prod_sub_category);

								$resultsLimits = 20;
								// get ranmdom 20 results from store
								$sql_check_val_query_re = "SELECT pics_up_names, pics_up_real_name, post_title, prod_ref, prod_prices, discount_price, discount_percentage FROM az_ldm__products WHERE prod_category = '$prod_category' AND prod_sub_category = '$prod_sub_category' ORDER BY RAND() LIMIT {$resultsLimits}";

								$qry_results = mysqli_query($this->conn, $sql_check_val_query_re);
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
							
								
							}
							else {
								$this->all_error_messages = "No products Found With Products Ref Of {$this->prod_ref}";
							}
						}
						else {
							$this->all_error_messages = msyqli_error($this->conn);
						}
					}
					else {
						$this->all_error_messages = "Invalid Products Category Name And/Or Sub Category Name";
					}
				}
				else {
					$this->all_error_messages = "Category Name And/Or Sub Category Name Is Empty";
				}
			}
		}

		public function show_all_sub_cat() {
			if ( $this->Get_all_sub_categories() === true ) {
				return $this->output_sub_categories;
			}
		}

		public function show_all_products_from_subCat() {
			if ( $this->sub_cat_name !== "" ) {
				if ($this->Get_all_products_from_sub_cat() === true ) {
					// return output messages
					return $this->output_messages;
				}
			}
		}


		public function show_all_outputs() {

			// check if catefory exists
			if ( $this->Check_if_categories_exists() === true ) {

				// check if any produstc exists in category or subcategory
				if ( $this->Check_if_any_products_exists_in_category() === true ) {
					// products exists in category 
					// now we get all products in categories
					if ( $this->output_prod_ref !== "" ) {
						if ( $this->Get_all_products_in_category() === true ) {
							// get all products in category
							if ( is_array($this->output_messages) && ( count($this->output_messages) > 0 ) ) {
								return $this->output_messages;
							}
						}
					}
				}
			}
		}

		public function show_error_messages() {
			if ( $this->all_error_messages !== "" ) {
				return $this->all_error_messages;
			}
		}
	}



 ?>