/**
 * THIS FUNCTION GET BROWSER NAME AND STORE IN DATABASE 
 * WITH INFO PASSED TO ( browser_get.php )
 * @param {[STRING: USERS_REF]} __users_ref [users ref for user info]
 * NOTE: CHANGE  __recent_page.protocol FROM http TO https WHEN DONE WITH TESTING
 * NOTE: CHECK browser_get.php FILE IMPORTANT!!! 
 * DELETE THE 2 NOTES ON THIS PAGE LINE WHEN TESTING IS DONE
 */
function GetUsersBrowser( __users_ref, __hidden_field = false, __form_id = "" ) {
    if ( __users_ref !== "" ) {
        // Opera 8.0+
        var isOpera = (!!window.opr && !!opr.addons) || !!window.opera ||
        navigator.userAgent.indexOf(' OPR/') >= 0;
        // Firefox 1.0+
        var isFirefox = typeof InstallTrigger !== 'undefined';
        // At least Safari 3+: "[object HTMLElementConstructor]"
        var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
        // Internet Explorer 6-11
        var isIE = /*@cc_on!@*/false || !!document.documentMode;
        // Edge 20+
        var isEdge = !isIE && !!window.StyleMedia;
        // Chrome 1+
        var isChrome = !!window.chrome && !!window.chrome.webstore;
        // Blink engine detection
        var isBlink = (isChrome || isOpera) && !!window.CSS;

        // check if one of it true 
        if ( isOpera || isFirefox || isSafari || isIE || isEdge || isChrome || isBlink ) {
            var browser;
            if ( isOpera ) {
                browser = "Opera";
            }
            else if ( isFirefox ) {
                browser = "Firefox";
            }
            else if ( isSafari ) {
                browser = "Safari";
            }
            else if ( isIE ) {
                browser = "InternetExplorer";
            }
            else if ( isEdge ) {
                browser = "Edge";
            }
            else if ( isChrome ) {
                browser = "Chrome";
            }
            else if ( isBlink ) {
                browser = "Blink";
            }
            else {
                "NoBrowser";
            }

            if ( browser !== "" ) {
                var __recent_page = window.location; // windows location

                if ( __recent_page.protocol === "http:" ) {
                    __recent_page = encodeURIComponent(__recent_page);

                    // check if hidden field is set to true
                    if ( __hidden_field === true ) {
                        // add hidden inputs instead of sending data to datebase
                        if ( __form_id !== "" ) {
                            document.querySelector("form[id="+__form_id+"]").innerHTML += "<input type='hidden' name='az_ldm_bwrs_info' value='"+browser+"'>";

                            document.querySelector("form[id="+__form_id+"]").innerHTML += "<input type='hidden' name='az_ldm_request_page' value='"+__recent_page+"'>";
                        }
                        else {
                            show_all_message_popup("Form Id Must be Set.. To Get Broswer Info", "error");
                        }
                    }
                    else {
                        $.ajax({
                            url: "./browsers_get.php?browser="+browser+"&page_request="+__recent_page+"&us_id="+__users_ref,
                            type: "GET",
                            asynchronous: false,
                            success: function() {
                                //show_message_popup('Getting Broswer Info', 'success');
                            },
                            complete: function (response) {
                                //show_message_popup('Broswer Info Gotten Successfully', 'success');
                            },
                            error: function () {
                                //show_message_popup('Cannot Get Browser Info', 'error');
                            }
                        });
                    }
                }
            }
        }
    }
}