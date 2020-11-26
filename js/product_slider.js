if ( document.getElementsByClassName("az_ldm_product__image").length > 1 ) {

  var slideIndex = 1;

  var myTimer;
  var slideToggleHovered  = false;
  var slideToggleMouseOut = false;
  var sliderToggleId = "NULL";


if ( document.querySelector(".az_ldm_side_image_toggle") !== undefined ) {
  // show side image toggle
  var sideImageToggle = document.querySelector(".az_ldm_side_image_toggle");
    var innerImageToggleSlider = document.querySelectorAll(".az_ldm_side_image_toggle span"); 
    innerImageToggleSlider.forEach((element) => {
      element.style.opacity = '.5';
      element.style.opacity = '.5';

      element.addEventListener("mouseover", function () {
        var that = this;

        // get contents id
        sliderToggleId = that.getAttribute("id");   // slidder and toggle slider random id
        that.style.opacity = '1';
        slideToggleHovered = true;
      });

      ;['mouseleave', 'mouseout'].forEach(eventName => {
        if ( eventName !== "" ) {
          // add opaccity when image slider toggle is not on hover
          element.addEventListener(eventName,  function () {
            this.style.opacity = '.5';
            slideToggleMouseOut = true;
          });
        }
      });
    });
}

  var slideshowContainer;
  window.addEventListener("load",function() {
      showSlides(slideIndex);
      myTimer = setInterval(function(){plusSlides(1)}, 4000);
    
      //COMMENT OUT THE LINE BELOW TO KEEP ARROWS PART OF MOUSEENTER PAUSE/RESUME
      //slideshowContainer = document.getElementsByClassName('az_ldm_product__imageCont')[0];
    
      //UNCOMMENT OUT THE LINE BELOW TO KEEP ARROWS PART OF MOUSEENTER PAUSE/RESUME
      slideshowContainer = document.getElementsByClassName('az_ldm_product__imageCont')[0];
    
    	// UNCOMMENT OUT THIS LINE TO ALLOW PAUSE ON MOUSE OVER AND MOUSE LEAVE
      // slideshowContainer.addEventListener('mouseenter', pause)
      // slideshowContainer.addEventListener('mouseleave', resume)
  })

  // NEXT AND PREVIOUS CONTROL
  function plusSlides(n){
    clearInterval(myTimer);
    if (n < 0){
      showSlides(slideIndex -= 1);
    } else {
     showSlides(slideIndex += 1); 
    }
    
    //COMMENT OUT THE LINES BELOW TO KEEP ARROWS PART OF MOUSEENTER PAUSE/RESUME
    
    if (n === -1){
      myTimer = setInterval(function(){plusSlides(n + 2)}, 4000);
    } else {
      myTimer = setInterval(function(){plusSlides(n + 1)}, 4000);
    }

  }

  //Controls the current slide and resets interval if needed
  function currentSlide(n){
    clearInterval(myTimer);
    myTimer = setInterval(function(){plusSlides(n + 1)}, 4000);
    showSlides(slideIndex = n);
  }

  function showSlides(n){
    var i;
    var slides = document.getElementsByClassName("az_ldm_product__image");
    if ( slides.length > 1 ) {
      var dots = document.getElementsByClassName("dot");
      var sliderImageToggle = document.querySelectorAll(".az_ldm_side_image_toggle span");

      if (n > slides.length) {slideIndex = 1}
        if (n > slides.length) {slideIndex = 1}
      if (n < 1) {slideIndex = slides.length}
      for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
      }
      for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
      }
      if ( sliderImageToggle !== undefined ) {
        for( i = 0; i < sliderImageToggle.length; i++ ) {
            sliderImageToggle[i].className = sliderImageToggle[i].className.replace(" active", "");
        }
      }
      slides[slideIndex-1].style.display = "flex";

      dots[slideIndex-1].className += " active";
       if ( sliderImageToggle !== undefined ) {
          sliderImageToggle[slideIndex-1].className += " active";
       }
    }
  }

  pause = () => {
    clearInterval(myTimer);
  }

  resume = () =>{
    clearInterval(myTimer);
    myTimer = setInterval(function(){plusSlides(slideIndex)}, 4000);
  }

  // UNCOMMENT OUT THIS LINE TO SHOW DOTS
  // var dots_wrapper = document.querySelector('.side_dot').style.display = "none";
}