// $(window).on('load', function (){

// });
// add sticky header on scroll
	var stickyOffset = $('.sticky').offset().top;
	var breakPoint = stickyOffset += 50;

	$(window).scroll(function(){
	  var sticky = $('.sticky'),
	      scroll = $(window).scrollTop();

	  	if (scroll >= stickyOffset){
	  		sticky.addClass('az_ldm_top_fixed');
	  	}
	  	else {
	  		sticky.removeClass('az_ldm_top_fixed');
	  	}
	});

if ( $('.az_ldm__top_container_wrapper .az_ldm_cat__head') !== null ) {
	$('.az_ldm__top_container_wrapper .az_ldm_cat__head').click(function(){
		var that = $(this).parent();
		$('.az_ldm_cat__head span .down').hide("slow");
		$('.az_ldm_cat__head span .right').show("slow");
		$('.az_ldm_cat__inner').hide("slow");
		that.find('.az_ldm_cat__head span .right').hide("slow");
		that.find('.az_ldm_cat__head span .down').show("slow");
		var innerCatCont = that.find('.az_ldm_cat__inner');
		that.find('.az_ldm_cat__head span .right').css('transform', 'rotate(90deg)');
		innerCatCont.toggle('slow');
		$('.az_ldm__top_container_wrapper .az_ldm_cat__head').click(function() {
			$(this).find('.az_ldm_cat__inner').hide();
		});
	});
}



function priceFormat(__selector, __priceAttr) {
	var __price = $(__selector).attr(__priceAttr);
	if ( __price !== null || __price !== undefined ) {
		var PriceToNumber = parseInt(__price);
		return PriceToNumber.toLocaleString();
	}
}