<?php 
	/**
	 * THIS CLASS FILE CHECKS IF USERS TRULY EXISTS
	 */
	
	class Az_check_if_exists {

		protected $clNAme; 			// column name
		protected $clVal; 			// coluimn value
		protected $tblName; 		// table name
		protected $outputMessage; 	// all output messages

		public function __construct( $clName, $clVal, $tblName = "",  $outputMessage = "" ) {
			$this->clName 			= $clName; 			// column name
			$this->clVal 			= $clVal; 			// column value
			$this->tblName 			= $tblName; 		// table name
			$this->outputMessage 	= $outputMessage; 	// all output message
		}
		public function check_val( $conn ) {
			if ( $this->clVal !== "" ) {
				if ( preg_match("/^[a-zA-Z0-9_]+$/", $this->clVal) ) {
					$this->clVal = preg_replace("#[^a-zA-Z0-9_]#", "", $this->clVal);
					$this->clVal = mysqli_real_escape_string( $conn, $this->clVal ); 
					if ( $this->tblName !== "" ) {
						$table_name = "az_ldm__".$this->tblName;
					} 
					else {
						$table_name = "az_ldm__users";
					}
					if ( $chVal_pre = $conn->prepare("SELECT {$this->clName} FROM {$table_name} WHERE {$this->clName} = ? ORDER BY id DESC") ) {
						$chVal_pre->bind_param( "s", $this->clVal );
						$chVal_pre->execute();
						$chVal_pre->store_result();
						// check if records exists 
						if ( $chVal_pre->num_rows > 0 ) {
							// records exists 
							$this->outputMessage = "Records Exists";
							return true;
						}
						else {
							$this->outputMessage = "No Records";
							return false;
						}
						$chVal_pre->close();
					}
				}
				else {
					$this->outputMessage = "Invalid " . $this->clVal . " Entered";
				}
			} 
		}

	}


 ?>