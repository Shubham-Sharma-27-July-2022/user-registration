jQuery(document).ready(function () {
    $mo = jQuery;

    let otpType = momrpsingle.otpType;

    // return if the enabled form is not found
    if($mo(".mepr-signup-form").length <= 0 ) {
        return;
    }

    $mo("form.mepr-signup-form").each(function () {
        let form = $mo(this);
        let otpType = "";
        if (momrpsingle.otpType == "mo_mrp_single_phone_enable") {
            otpType = "phone";
        }
        else{
            otpType = "email";   
        }
        let fieldID = $mo("input[name='mepr_product_id']").val();
        
            
            let img = '<div style="display:table;text-align:center;">'+
                        '<img alt="Loading..." src="'+momrpsingle.imgURL+'">' +
                      '</div>';

            let verifyfield = '<div style="display:none;" class="mp-form-row mo_vetify_otp_field">'+
                                '<div class="mp-form-label">'+
                                '<label for="mo_vetify_otp_field">Enter Verification Code:*</label>'+
                                '<span class="cc-error" style="display: none;">Verification Code Required</span>'+
                                '</div>'+
                                '<input type="text" name="mo_vetify_otp_field" id="mo_vetify_otp_field" class="mepr-form-input" value="" required>'+
                                '</div>';

            let messagebox = '<div style="margin-top:2%">' +
                                '<div   id="mo_message'+fieldID+'" hidden="" ' +
                                        'style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;">' +
                                '</div>' +
                              '</div>';

            let errormessagebox = '<div style="margin-top:2%">' +
                                '<div   id="mo_error_message'+fieldID+'" hidden="" ' +
                                        'style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;">' +
                                '</div>' +
                              '</div>';

            let button = '<div style="margin-top: 2%;">' +
                            '<div class="">' +
                                '<button type="button" style="width:100%;" class="btn btn-default" ' +
                                        'id="miniorange_otp_token_submit'+fieldID+'" ' +
                                        'title="Please Enter your phone details to enable this.">'+
                                        momrpsingle.buttontext+
                                '</button>' +
                            '</div>' +
                          '</div>';

            let submitbutton = '<input type="button" id="mo_single_checkout_submit'+fieldID+'" name="mo_single_checkout_submit" '+
            'style="padding: 10px;" value="Verify & Sign Up"><br>';

            $mo(button+messagebox).insertAfter(($mo('input[name="'+momrpsingle.formkey+'"]').closest(".mp-form-row")));
            $mo(verifyfield).insertAfter($mo("#mo_message"+fieldID));

            let parentsubmitselector = $mo("#mepr_no_val").parent();

            $mo(submitbutton).insertBefore(parentsubmitselector);

            $mo(errormessagebox).insertAfter($mo("#mo_single_checkout_submit"+fieldID));

            $mo(".mepr-signup-form input[type='submit']").hide();

            $mo('#miniorange_otp_token_submit'+fieldID).click(function () {

                var e = $mo('input[name="'+momrpsingle.formkey+'"]').val();
                $mo('#mo_message'+fieldID).empty(),
                $mo('#mo_message'+fieldID).append(img),
                $mo('#mo_message'+fieldID).show(),

                $mo.ajax({
                    url: momrpsingle.siteURL,
                    type: "POST",
                    data: {
                        action: "momrp_single_send_otp",
                        security:momrpsingle.nonce,
                        user_phone: e,
                        user_email: e,
                    },
                    crossDomain: !0,
                    dataType: "json",
                    success: function (o) {
                        if (o.result === "success") {
                            $mo('#mo_message'+fieldID).empty(),
                            $mo(".mo_vetify_otp_field").show(),
                            $mo('#mo_message'+fieldID).append(o.message),
                            $mo('#mo_message'+fieldID).css("border-top", "3px solid green"),
                            $mo('input[name="'+fieldID+'"]').focus();
                        } else {
                            $mo('#mo_message'+fieldID).empty(),
                                $mo('#mo_message'+fieldID).append(o.message),
                                $mo('#mo_message'+fieldID).css("border-top", "3px solid red"),
                                $mo('input[name="'+fieldID+'"]').focus();
                        };
                    },
                    error: function (o, e, n) {}
                });
            });
    });
});