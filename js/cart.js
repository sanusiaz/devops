	$(document).ready(function (){
	$('.az_ldm_exit_cart_popup_btn').click(function () {
		$(this).parent().fadeOut("slow");
		$('.az_ldm_cart_products_check_footer').fadeIn("slow");
	});
	$('.az_ldm_cart_products_check_footer div').click(function (){
		$(this).parent().fadeOut("fast");
		$('.az_ldm_checkout_cont').fadeIn("slow");
	});


	// add event listener to delete products from carts
	$(".az_ldm_topCat__secContDeleteCart").click(function (){
		var that = $(this);
		var __products = that.parent();
			var __prod_ref 			= __products.attr("data-product-ref");													// products ref
			var __cart_id 			= that.parent().parent().parent().parent().parent().attr("data-cart-id");				// carts id
			var __carts_count 		= that.parent().parent().parent().parent().parent().attr("data-carts-count");			// Total No Of Carts
			var __carts_header 		= that.parent().parent().parent().parent().parent().find(".az_ldm_carts_innerHead");	// carts header
			var __total_price 		= $(".az_ldm_products_det_inner_wrapper.total_price").attr("total_price");
			var __prod_price 		= that.parent().find(".az_ldm_new__price[products_price]").attr("products_price");

		var data = {};
			data['prod_ref'] = __prod_ref;
			data['cart_id'] = __cart_id;

		$.ajax({
			url: "includes/contents/delete_carts_products.php",
			type: "GET",
			data: data,
			dataType: "JSON",

			beforeSend:function (){
				show_all_message_popup("Sending Request", "success");
			},

			success: function () {
				show_all_message_popup("Deleting Products From Carts", "success");
			},

			complete: function (response) {
				var __data = response.responseText;
				if ( __data !== "" ) {
					__data = JSON.parse(__data);
					if ( __data.split("az(ldm)").pop() === "success" ) {
						__data = __data.split("az(ldm)")[0];
						// get deleted products price
						show_all_message_popup( __data.split("az(ldm_price)")[0], "success");
						__carts_count = __carts_count - 1;
						__carts_header.html("All Carts("+__carts_count+")");
						$('.az_ldm_products_det_inner_wrapper.total_carts span').html("<i class='icon ion-ios-list check_icon size-18'></i>"+__carts_count);
						$("[data-cart-id="+__cart_id+"]").attr('data-carts-count', __carts_count);
						$('.carts_count').html(__carts_count);
						if ( __carts_count < 1 ) {
							// carts is empty remove all contents
							$('.carts_count').remove();
							$('.all_carts_contents').html("<div style='font-weight: bolder; font-size: 20px; text-align: center; line-height: 300px;'>Aw Snap :) Your Cart Is Empty</div>");

							return;

						}	
						// differcence between total price and products price
						var price_diff = __total_price - __prod_price;
							$(".az_ldm_products_det_inner_wrapper.total_price[total_price] span p")[0].textContent = price_diff.toLocaleString();
							$(".az_ldm_products_det_inner_wrapper.total_price[total_price]").attr('total_price', price_diff);
							__products.remove();

					}
					else {
						// show error messages 
						show_all_message_popup(__data ,"error");
					}
				}
			},

			error: function (err) {
				// console.log("An Error Occured Please try Again Later");
				show_all_message_popup("An Error Occured Please try Again Later", "error");
			}
		});
	});

	// add events listener to update products quantity colors and sizes in carts store 
		$("select[name=ldm_product_quantity]").change(function () {
			var __Selector = $(this);
			var __url = 'cart_update.php';
			var __update = "prod_quantity";
			var __prod_ref = $(this).parent().parent().parent().parent().attr("data-product-ref");
			Update_Selectes_products_in_cart(__Selector, __update, __prod_ref, __url);
		});

		$("select[name=ldm_products_sizes]").change(function () {
			var __Selector = $(this);
			var __url = 'cart_update.php';
			var __update = "prod_size";
			var __prod_ref = $(this).parent().parent().parent().parent().attr("data-product-ref");
			Update_Selectes_products_in_cart(__Selector, __update, __prod_ref, __url);
		});

		$("select[name=ldm_products_colors]").change(function () {
			var __Selector = $(this);
			var __url = 'cart_update.php';
			var __update = "prod_color";
			var __prod_ref = $(this).parent().parent().parent().parent().attr("data-product-ref");
			Update_Selectes_products_in_cart(__Selector, __update, __prod_ref, __url);
		});

	/**
	 * THIS FUNCTION UPDFATES CARTS PRODUCTS INFO ON CHANGE
	 * @param {STRING} __Selector [VARIABLE ON WHICH THE EVENT OCCURED ON]
	 * @param {STRING} __update   [FIELD TO UPDATE IN DB]
	 * @param {STRING} __url      [URL]
	 */
	function Update_Selectes_products_in_cart( __Selector, __update, __prod_ref, __url ) {
		if ( typeof(__url ) === 'string' && typeof( __update ) === 'string' ) {
			if ( __update !== "" && __Selector !== "" ) {
					var data = {};
				var __name = __Selector.attr("name");
					var __value = __Selector.val();
				var __totalprice = $(".az_ldm_products_det_inner_wrapper.total_price[total_price]")[0].textContent;

				data['new_value']			= __value;
				data['up_clmn'] 			= __update;
				data['prod_ref']			= __prod_ref;

				$.ajax({
					url: __url,
					type: "POST",
					data: data,
					dataType: "JSON",

					beforeSend: function () {
						show_all_message_popup("Sending Request", "success");
					},

					success: function () {
						show_all_message_popup("Request Sent.. Waiting For Response", "success");
					},

					complete: function (response) {
						// console.log(response);
						if ( response !== "" ) {
							var __data = response.responseText;
							var __data = JSON.parse(__data);
							if ( __data.split("az(ldm)")[1] !== undefined ) {
								if ( __data.split("az(ldm)")[1] !== null && __data.split("az(ldm)")[1] === "success" ) {
									// show succes messages
									show_all_message_popup(__data.split("az(ldm)")[0].split("az(ldm_price)")[0], "success");
									var __prod_price = __Selector.parent().parent().parent().parent().find(".az_ldm_new__price[products_price]")[0].textContent;
									var __recent_total_price = $(".az_ldm_products_det_inner_wrapper.total_price").attr("total_price");

									if ( __update === "prod_quantity" ) {
										// get the different between total price and products price
										var __diff_price =  __recent_total_price - __prod_price;
											__diff_price = __diff_price.toLocaleString();
										$(".az_ldm_products_det_inner_wrapper.total_price[total_price] span p")[0].textContent = __diff_price;

										var __prod_price__ = __data.split("az(ldm)")[0].split("az(ldm_price)")[1];
											var __recent_price_withOut_discount = __data.split("az(ldm)")[0].split("az(ldm_price)")[2];
										// get new total price from store
										var __new_total_price 			= __data.split("az(ldm)")[0].split("az(ldm_price)").pop();
											__new_total_price_raw 		= parseInt(__new_total_price);
											__new_total_price 			= __new_total_price_raw.toLocaleString();
										// change total price in cart
										$(".az_ldm_products_det_inner_wrapper.total_price[total_price] span p")[0].textContent = __new_total_price;
										$(".az_ldm_products_det_inner_wrapper.total_price").attr("total_price", __new_total_price_raw);

										// change products price to products recent price * products quantity
										var __recent_new_products_price_raw = __prod_price__ * __value;
											__recent_new_products_price 	= __recent_new_products_price_raw.toLocaleString();
										__Selector.parent().parent().parent().parent().find(".az_ldm_new__price[products_price] span")[0].textContent = __recent_new_products_price;

										__Selector.parent().parent().parent().parent().find(".az_ldm_new__price[products_price]").attr("products_price", __recent_new_products_price_raw);

										if ( __Selector.parent().parent().parent().parent().find(".az_ldm_discount__price span") !== undefined ) {
												var discountMulValuePrice = __recent_price_withOut_discount * __value;
													discountMulValuePrice = discountMulValuePrice.toLocaleString();
											__Selector.parent().parent().parent().parent().find(".az_ldm_discount__price span")[0].textContent = discountMulValuePrice;
										} 
									}
								}
								else {
									// treat all message as error messages
									show_all_message_popup(__data, "error");
								}
							}
							else {
								show_all_message_popup(__data, "error");
							}
						}
						else {
							show_all_message_popup("Output Messages Is Empty", "error");
						}
					},

					error: function () {
						show_all_message_popup("An Error Occured Please Try Again Later" ,"error");
					}
				});
			}
			else {
				if ( __Selector === "" ) {
					show_all_message_popup("Cart Update Selector Value Cannot be Empty", "error");
				}
				else {
					show_all_message_popup("Update Value Cannot be Empty", "error");
				}
			}
		}
	}
});