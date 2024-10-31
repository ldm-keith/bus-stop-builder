jQuery(document).ready(function ($) {
	    jQuery('#formModalThanksLabel').hide(); // Hide the thanks header initially
    jQuery(document).on('click', '#save', function () {
        console.log("Modal triggered");
        jQuery("#formModal").appendTo("body").modal("show");
        // Get the value of the "filename" cookie
        let filenameCookie = getCookie('filename');
        var json_urlField = jQuery('#input_1_5');
        // Check if the cookie exists and set the hidden field value
        if (filenameCookie) {
            jQuery('#input_1_4').val(filenameCookie);
            json_urlField.val(json_urlField.val() + filenameCookie);
        }
    });
    jQuery(document).on('click', '#gform_submit_button_1', function (e) {
        e.preventDefault(); // Prevent default form submission
        console.log("Form Submit triggered");
        // Now trigger the form submission
        jQuery("#gform_1").trigger("submit", [true]);
    });
    jQuery(document).on('gform_confirmation_loaded', function (event, formId) {
        // Replace 1 with your Gravity Form ID
        if (formId == 1) {
            // Trigger the button click
            jQuery('#hidden-submit-btn').click();
            // jQuery('#formModal button.close').click();
            jQuery('#formModal button.close').addClass('close_redirect');
//             jQuery('#formModal #formModalLabel').html('Thank You');
            jQuery('#formModalLabel').hide(); // Hide the initial header
        jQuery('#formModalThanksLabel').show(); // Show the thanks header
			console.log('Form Submitted');
            // Delete the "filename" cookie
            deleteCookie('filename');
        }
    });

    jQuery("#formModal").on("hidden.bs.modal", function () {
        if (jQuery("#formModal .close").hasClass("close_redirect")) {
            console.log("Close Triggered after successfull submission");
            jQuery('#hidden-redirect-btn').click();
        }
    });
});

function getCookie(name) {
    let cookieArr = document.cookie.split(";");

    for (let i = 0; i < cookieArr.length; i++) {
        let cookiePair = cookieArr[i].split("=");

        if (name == cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }

    return null;
}

// Function to delete a cookie
function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}