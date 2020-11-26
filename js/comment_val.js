/**
 * Validate coments before sending requests
 */

// validate forms
$("form#commentsForm").submit(function (e) {
	var that = $(this);
	// validate comments forms
	var defaultBorderColor = "#216ea7";
	var errorBorderColor = "#909d01";

	// validates all inputs
	if ( $("input[name=comment_users_first_name]").val() === "" && $("input[name=comment_users_last_name]").val() === "" && $("textarea[name=comments_users_message]").val() === "" ) {
		show_all_message_popup( "Fill All Inputs Fields" ,"error");
		return false;
	}
	else if ( $("input[name=comment_users_first_name]").val() === "" ) {
		show_all_message_popup( "Please Enter Your First Name" ,"error");
		return false;
	}
	else if ( $("input[name=comment_users_last_name]").val() === "" ) {
		show_all_message_popup( "Please Enter Yout Last Name" ,"error");
		return false;
	}
	else if ( $("textarea[name=comments_users_message]").val() === "" ) {
		show_all_message_popup( "Message Field Is Required" ,"error");
		return false;
	}
	else {
		// send form data
		var url = that.attr("action");		// url 
		var type = that.attr("method");		// type / method
		var data = {};							// data

		that.find('[name]').each(function() {
			var __that = $(this);
			var __name = __that.attr("name");		// name attributes
			var __value = __that.val();				// values 

			if ( __value !== "" ) {
				data[__name] = __value;		// data
			}
		});

		/**
		 * SEND REQUEST TO ADD NEW COMMENTS TO STORE FOR EACH PRODUCTS VIA PRODUCTS REF/ID
		 * @param  {STRING} options.url:       url           [url to destination request]
		 * @param  {STRING} options.type:      type          [type of the request either GET / POSTS]
		 * @param  {OBJECK} options.data:      data          [data attributes holder name as key and val() as value]
		 * @param  {STRING} options.dataType:  "JSON"        [specified data type for response and request]
		 * @param  {FUNCTION} options.beforeSend :             ()            [acts before sending request]
		 * @param  {FUNCTIONS(success)} success:           ()            [acts on success / acts when request is sent]
		 * @param  {FUNCTIONS(complete)} complete:          (response)    [when request is completed]
		 * @param  {FUNCTIONS(error)} error:             (err          [acts when an error occured in getting request]
		 */
		$.ajax({
			url: url,
			type: type,
			data: data,
			dataType: "JSON",

			beforeSend : () => {
				show_all_message_popup( "Preparing Request", "success");
			},
			success: () => {
				show_all_message_popup( "Request Sent Waiting For Response", "error");
			},
			complete: (response) => {
				if ( response.status !== 404 ) {
					var __data = response.responseText;
					if ( __data !== "" ) {
						__data = JSON.parse(__data);
						// check if response recieved is a success message
						if ( __data.split("az(ldm)").pop() === "success" ) {
							// show success messages
							show_all_message_popup( __data.split("az(ldm)")[0].split("az(ldm_results)")[0], "success");

							var __Message 		= __data.split("az(ldm)")[0].split("az(ldm_results)")[1];		// message
							var __curTimeStamp 	= __data.split("az(ldm)")[0].split("az(ldm_results)")[2];		// surrent time stamp
							var __CharNames 	= __data.split("az(ldm)")[0].split("az(ldm_results)")[3];		// first characters of users first names and last names
							var __randomColors = Math.ceil( Math.random() * 1000000 );		// random Colors

							if ( document.querySelector("#products_comments .az_ldm_products__reviews_inner .az_ldm_products__reviews_secContWrapper .az_ldm__secInenrCont") === null ) {
								$("#products_comments .az_ldm_products__reviews_inner .az_ldm_products__reviews_secContWrapper span").remove();
							}

							// show recent added comments
							document.querySelector("#products_comments .az_ldm_products__reviews_inner .az_ldm_products__reviews_secContWrapper").innerHTML += 
								"<div class='az_ldm__secInenrCont'>" +
									"<div class='az_ldm__secInenrCont_txtImage' style='background-color: #"+__randomColors+"; color: white;'>"+ __CharNames +"</div>" +
									"<div class='az_ldm__secInenrCont_date'>"+__curTimeStamp+"</div>" +
									"<div class='az_ldm__secInenrCont_message'>"+ __Message+
									"</div>" +
								"</div>";
							$(".az_ldm__products__comments_wrapper").fadeOut("fast");
						}
						else {
							// show error messages
							show_all_message_popup( __data.split("az(ldm)")[0], "error");
						}
					}
				}
				else {
					show_all_message_popup( "Invalid Request.. Please Try Again Later" , "error");
				}
			},

			error: err => show_all_message_popup( err, "error")
		});
	}
	e.preventDefault();
});