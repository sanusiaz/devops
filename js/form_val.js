function Reload_Captch_text() {
	$.ajax({
        url: 'http://localhost/lumiere/captch_restall.php',
        type: 'POST',
        asynchronous: false,
        complete: function (response) {
			var checkedCaptchInputs = $('input[name=txt_captch_hidden]').val();
			var checkedInputsUsers = $('input[name=txt_captch]').val();
            var data = response;
                var dataRE = data.responseText;
            	dataRE = JSON.parse(dataRE);
            	// get text image and catch text
            	let __captch 			= dataRE.split('( az__(ldm) )');
            	let CaptchImage 		= __captch[0]; // captch image
            	let captchTextFromImage = __captch[1]; // captch text
                // change input hidden value to new data gotten from captch page
                $('input[name=txt_captch_hidden]').attr('value',captchTextFromImage);
                $('input[name=txt_captch_hidden]').val(captchTextFromImage);
                $('.txt_captch').html("<img src='"+CaptchImage+"'>");

            if ( dataRE !== "success" ) {
                //show_message_popup(dataRE, 'error');
                return false;
            }
        },
        error: function () {
            show_message_popup('An Error Occured While Loading Captch Please Try Again later', 'error');
        }
    });
}


// get inputs val
var checkedCaptchInputs = $('input[name=txt_captch_hidden]').val();
var defaultBorderColor = '#216EA7';

if ( $('input[name=txt_captch_hidden]').val() !== "" ) {
	$('input[name=txt_captch]').on('change', function () {
		if ( $('input[name=txt_captch_hidden]').val() !== $('input[name=txt_captch]').val() ) {
			$('input[name=txt_captch]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Capthched Text Must Be The Same', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else {
			$('input[name=txt_captch]').css('border-color', defaultBorderColor);
		}
	});
}


$('#az_step_0 input').change(function() {
	$(this).css('border-color', defaultBorderColor);
});
$('.az_nxt_step.frm_next').click(function (){
		// check if we are on login page
	if ( window.loation = "login.php" ) {
		if ( $("#az_step_0 input[name*=users_]").val() === "" ) {
			Reload_Captch_text();	//reload captch
			show_message_popup( "Fill All Inputs", "error");
			$('#az_step_0 input').css('border-color', 'rgb(168, 15, 15)');
		}
		else if ( $("#az_step_0 input[name=users_email_address]").val() === "" ) {
			Reload_Captch_text();	//reload captch
			$('#az_step_0 input[name=users_email_address]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup( "Email Address Cannot Be Empty" ,"error");
		}
		else if ( $("#az_step_0 input[name=users_password]").val() === "" ) {
			Reload_Captch_text();	//reload captch
			$('#az_step_0 input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup( "Password Cannot Be Empty" ,"error");
		}
		else if ( $("#az_step_0 input[name=users_password]").val() === "" && $("#az_step_0 input[name=users_re_password]").val() === "" ) {
			$('#az_step_0 input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			Reload_Captch_text();	//reload captch
			$('#az_step_0 input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup( "Passwords Fields Cannot be Empty" ,"error");
		}
		else if ( $("#az_step_0 input[name=users_re_password]").val() === "" ) {
			Reload_Captch_text();	//reload captch
			$('#az_step_0 input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup( "Re-Enter Your Password" ,"error");
		}
		else if ( $("#az_step_0 input[name=users_re_password]").val() !== $("#az_step_0 input[name=users_password]").val() ) {
			$('#az_step_0 input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			Reload_Captch_text();	//reload captch
			$('#az_step_0 input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup( "Passwords Do Not Match" ,"error");
		}
		else {
			var step = $('[id *= az_step_ ]').length;
			$(this).parent('[id *= az_step_]').fadeOut(500);
			var starting_id = $(this).parent().attr('id');
			var starting_in =  starting_id.split('_').pop();
			for ( i = starting_in; i < step; i++ ) {
				var step_holder = "az_step_"+[i];
				setTimeout(function (){
					$("#"+step_holder).fadeIn('slow');
				}, 500);
				if ( [i] > starting_in ) {
					break;
				}
			}
		}
	}
	else {
		if ( $('#az_step_0 input[name=users_name]').val() === "" && $('#az_step_0 input[name=users_email_address]').val() === "" && $('#az_step_0 input[name=users_re_email_address]').val() === "" && $('#az_step_0 input[name=users_password]').val() === "" && $('#az_step_0 input[name=users_re_password]').val() === "" ) {
			$('#az_step_0 input').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill all inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_name]').val() === "" && $('#az_step_0 input[name=users_email_address]').val() === "" && $('#az_step_0 input[name=users_re_email_address]').val() === "" && $('#az_step_0 input[name=users_password]').val() === "" && $('#az_step_0 input[name=users_re_password]').val() === "" ) {
			$('#az_step_0 input').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill all inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('form #az_step_0 input[type=text]').val() === ""  && $('form #az_step_0 input[type=email]').val() === "" ) {
			$('#az_step_0 input').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill All Inputs', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('form input[name=senders_name]').val() === "" ) {
			$('#az_step_0 input[name=senders_name]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Senders name cannot be empty', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('form input[name=senders_email]').val() === "" ) {
			$('#az_step_0 input[name=senders_email]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Senders Email cannot be empty', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('form input[name=reciever_email]').val() === "" ) {
			$('#az_step_0 input[name=reciever_email]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Recievers Email cannot be empty', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_name]').val() === "" && $('#az_step_0 input[name=users_email_address]').val() === "" ) {
			$('#az_step_0 input[name=users_name]').css('border-color', 'rgb(168, 15, 15)');
			$('#az_step_0 input[name=users_email_address]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill Both Email Fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_name]').val() === "" ) {
			$('#az_step_0 input[name=users_name]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Enter Your Name', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_email_address]').val() === "" ) {
			$('#az_step_0 input[name=users_email_address]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill Email inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_re_email_address]').val() === "" ) {
			$('#az_step_0 input[name=users_re_email_address]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill Email inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_password]').val() === "" && $('#az_step_0 input[name=users_re_password]').val() === "" ) {
			$('#az_step_0 input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			$('#az_step_0 input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill Both Passwords Fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_password]').val() === "" ) {
			$('#az_step_0 input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill passwords inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else if ( $('#az_step_0 input[name=users_re_password]').val() === "" ) {
			$('#az_step_0 input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			// show error message
			show_message_popup('Fill passwords inputs fields', 'error');
			Reload_Captch_text(); // reload captch
			return false;
		}
		else {
			// check if email address match
			if ( $('#az_step_0 input[name=users_email_address]').val() === $('#az_step_0 input[name=users_re_email_address]').val() ) {
				$('input[name=users_email_address]').css('border-color', defaultBorderColor);
				$('input[name=users_re_email_address]').css('border-color', defaultBorderColor);
				// check if password match
				if ( $('#az_step_0 input[name=users_password]').val() === $('#az_step_0 input[name=users_re_password]').val() ) {
					$('input[name=users_password]').css('border-color', defaultBorderColor);
					$('input[name=users_re_password]').css('border-color', defaultBorderColor);
					var step = $('[id *= az_step_ ]').length;
					$(this).parent('[id *= az_step_]').fadeOut(500);
					var starting_id = $(this).parent().attr('id');
					var starting_in =  starting_id.split('_').pop();
					for ( i = starting_in; i < step; i++ ) {
						var step_holder = "az_step_"+[i];
						setTimeout(function (){
							$("#"+step_holder).fadeIn('slow');
						}, 500);
						if ( [i] > starting_in ) {
							break;
						}
					}
				}
				else {
					$('#az_step_0 input[type=password]').css('border-color', 'rgb(168, 15, 15)');
					// show error message
					show_message_popup('Password Do Not Match', 'error');
					Reload_Captch_text(); // reload captch
					return false;
				}
			}
			else {
				$('#az_step_0 input[type=email]').css('border-color', 'rgb(168, 15, 15)');
				// show error message
				show_message_popup('Email Address Do Not Match', 'error');
				Reload_Captch_text(); // reload captch
				return false;
			}
		}
	}
});
$('.az_nxt_step.frm_prev').click(function (){
	$(this).parent().fadeOut(500);

	var step = $('[id *= az_step_ ]').length;
	var starting_id = $(this).parent().attr('id');
	var starting_in =  starting_id.split('_').pop();
	for ( i = starting_in; i < step; i-- ) {
		setTimeout(function (){
			$("#az_step_"+[i]).fadeIn('slow');
		}, 500);
		if ( [i] < starting_in ) {
			break;
		}
	}
});

$('#az_login__cont input').change(function(event) {
	/* chenge the default border color */
	$(this).css('border-color', defaultBorderColor);

});
$('input[type=submit]').click(function(event) {
	/* Check if all inputs is not empty */
	if ( $('#az_login__cont input[name=users_email_address]').val() === "" && $('#az_login__cont input[name=users_password]').val() === "" && $('#az_login__cont input[name=users_re_password]').val() === "" && $('#az_login__cont input[name=txt_captch]').val() === "" ) {
		// show error message
		show_message_popup('Fill all inputs fields' ,'error');
		// chenge all border inputs for all fields
		$('input[name=users_email_address]').css('border-color', 'rgb(168, 15, 15)');
		$('input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
		$('input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
		$('input[name=txt_captch]').css('border-color', 'rgb(168, 15, 15)');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('#az_login__cont input[name=users_password]').val() !== $('#az_login__cont input[name=users_re_password]').val() ) {
		// show error message
		if ( $('input[name=users_password]').val() === "" ) {
			$('input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup('Enter Your Password' ,'error');
		}
		else if ( $('input[name=users_re_password]').val() === "" ) {
			$('input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup('Re-Enter Your Password' ,'error');
		}
		else {
			$('input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
			$('input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
			show_message_popup('Password fields do not match' ,'error');
		}
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=users_name]').val() === '' ){
		show_message_popup('All fields cannot be empty', 'error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=users_email_address]').val() === "" || $('input[name=users_re_email_address]') === "" ) {
		show_message_popup('Email Address Cannot Be Empty', 'error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=users_password]').val() === "" || $('input[type=users_re_password]').val() === "" ) {
		$('input[name=users_password]').css('border-color', 'rgb(168, 15, 15)');
		$('input[name=users_re_password]').css('border-color', 'rgb(168, 15, 15)');
		show_message_popup('Password fields cannot be empty', 'error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('textarea[name=account_recovery_question]').val() === "" ) {
		show_message_popup( 'Enter a recovery question for your account','error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('textarea[name=account_recovery_answer]').val() === "" ) {
		show_message_popup( 'Enter a recovery answer for your account','error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=txt_captch]').val() === "" ) {
		// show error message
		$('input[name=txt_captch]').css('border-color', 'rgb(168, 15, 15)');
		show_message_popup('Capthched Text Field Cannot Be Empty', 'error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=txt_captch_hidden]').val() !==  $('input[name=txt_captch]').val() ) {
		// show error message
		show_message_popup('Capthched Text Must Be The Same', 'error');
		Reload_Captch_text(); // reload captch 
		return false;
	}
	if ( $('input[name=txt_captch]').val() === "" ) {
		show_message_popup( 'Please Fill Out The Capthched Field','error');
		$('input[name=txt_captch]').css('border-color', 'rgb(168, 15, 15)');
		$('input[name=txt_captch]').change(function(event) {
			/* Act on the event */
			$('input[name=txt_captch]').css('border-color', defaultBorderColor);
		});
		Reload_Captch_text(); // reload captch 
		return false;
	}
});



function show_message_popup(message, status) {
	if ( message !== "" ) {
		$('.az_all_signup_message').html(
				"<div class='az_message_txt'>"+ 
					message
				+"</div>");
		$('.az_all_signup_message').fadeIn('fast');
		setTimeout(function(){
			$('.az_all_signup_message').fadeOut('slow');
		}, 3000);
		if ( status === 'error' ) {
			$('.az_all_signup_message').css('border-left', '7px solid rgb(168, 15, 15)');
		}
		else {
			$('.az_all_signup_message').css('border-left', '7px solid rgb(15, 168, 60)');
		}
	}
}

// set timeout for all pop-up messages
setTimeout( function (){
	$('.az_pop_message').fadeOut('slow');
	$('.az_pop_message').remove();
}, 10 * 3600 )