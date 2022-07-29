let $mo = jQuery;
jQuery(document).ready(function () {

		let fieldText = 'Verify Field';
		let otpType = customForm.otpType;
		let formID  = '1';
		let fieldID = 'phone';
		let fieldSelector = customForm.fieldSelector.includes(".") ? $mo(customForm.fieldSelector.replace(/ /g,'.')) : $mo(customForm.fieldSelector);
		let submitSelector = customForm.submitSelector.includes(".") ? $mo(customForm.submitSelector.replace(/ /g,'.')) : $mo(customForm.submitSelector);
		let formVar = $mo(submitSelector).closest("form");

		if( typeof(formVar) == 'undefined'){

			formVar = $mo(fieldSelector).closest("form") ;
		}



				let thisForm = $mo(formVar);
                let insertSelector  =   $mo(customForm.fieldSelector);
                if(thisForm.find(insertSelector).length > 0 && thisForm.find(submitSelector).length > 0){
                    moAjaxInitializer(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,customForm);
                    preventSubmitButton(submitSelector,fieldSelector,otpType,formID);            
                }



     });




function preventSubmitButton(submitSelector,fieldSelector,otpType,formID){
    $mo(submitSelector).click(function(e){
    e.preventDefault();

      $mo("#mo_message"+ otpType + formID).empty();
        $mo("#mo_message"+ otpType + formID).append(img);
        $mo("#mo_message"+ otpType + formID).hide();
    var phone = fieldSelector.val(); 
     $mo.ajax({

            url: customForm.siteURL,
            type: "POST",
            data: {
                user_phone: phone,
                otpType:otpType,
                action:customForm.saction,
            },
            crossDomain: !0, dataType: "json",
            success: function (o) {
                if(o.result=='success'){
                         $mo(submitSelector).unbind('click').click();
                }
                else{
                    $mo("#mo_message"+ otpType + formID).empty();
                    $mo('#mo_message' + otpType + formID).show();
                    $mo('#mo_message' + otpType + formID).append(o.message);
                    $mo("#mo_message"+ otpType + formID).css("border-top", "3px solid red");
                    $mo("#mo_message"+ otpType + formID).focus().keyup();
                }
            },
            error: function (o) {}
        });


        });



}
function moAjaxInitializer(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData){
    addButtonAndFields(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData);
    bindSendOTPButton(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData);
    bindVerifyButton(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData);
    // is_already_verified(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData);
    addValidatedIcon();
}


// to add icon to validated field 
function addValidatedIcon(){
    setTimeout( function(){
        let validated_icon = '<span class="dashicons dashicons-yes mo-validated-icon"></span>';
        if(!$mo(".mo-validated+.mo-validated-icon").length)
            $mo(validated_icon).insertAfter(".mo-validated");
    },250);
} 

//already validated and page refreshes, keep the tick mark
function is_already_verified(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData) {
    if(moData.validated[otpType]) {
        thisForm.find("#mo_send_otp_" + otpType + formID).attr('disabled',true).hide();
        fieldSelector.addClass("mo-validated");
    }
}

function addButtonAndFields(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData) {

    // messagebox template
    let messageBox = '<div  class="mo_message_box" ' +
                        'id="mo_message' + otpType + formID + '" >' +
                 '</div>';

    
    //Verification field
    let verifyField     =   '<div id="mo_verify_field_container'+otpType + formID +'" ' +
                                    'style="display:none;" ' +
                                    'class="row mo_verify_field_container">' +
                                '<div class="col-sm-12  single">' +
                                    '<div data-field-wrapper="'+fieldID+'" '+ 
                                        'class="form-group" ' +
                                        'id="'+otpType + formID +'-wrap">' +
                                        '<label id="'+otpType + formID +'Label" ' +
                                            'for="'+otpType + formID +'" '+
                                            'class="control-label">'+ moData.fieldText +
                                            '<span class="mo-field-message" style="color:#ee0000;">*</span></label>' +
                                        '<div class=""><input ' +
                                            'class=" form-control" id="mo_verify_otp_'+otpType + formID+'" name="mo_verify_otp_'+otpType + formID+'" value="">' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';
    
    //Verify Button
    let verifyOTPButton   =   '<div id = "mo_verify_otp_button-container' + otpType + formID + '" ' +
                                    'style="display:none;" ' +
                                    'class = "mo_verify_otp_button-container" >' +
                                '<input  type = "button" '+
                                        'name = "mo_verify_button_'+ otpType + formID + '" '+
                                        'class = "btn btn-default mo_verify_otp_button"  '+
                                        'id = "mo_verify_button_' + otpType + formID + '" '+
                                        ' value = "Verify OTP"/>'+
                            '</div >';

    //Send OTP button
    let sendOTPButton   =   '<div id = "mo_send_otp_button-container' + otpType + formID + '" ' +
                                'class = "mo_send_otp_button-container" >' +
                            '<input  type = "button" '+
                                    'name = "mo_send_otp_'+ otpType + formID + '" '+
                                    'class = "btn btn-default mo_send_otp_button" '+
                                    'id = "mo_send_otp_' + otpType + formID + '"'+
                                    ' value = "' + moData.buttontext + '"/>'+
                            '</div >';

    let html= sendOTPButton + messageBox + verifyField + verifyOTPButton ;
    $mo(html).insertAfter(insertSelector);
}

function bindSendOTPButton(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData) {
    img = "<img alt='' src='" + moData.imgURL + "'>"; // image HTML templates

    $mo('#mo_send_otp_' + otpType + formID).click(function(){
        var e = fieldSelector.val();
        var msg = "An One Time Passcode has been sent successfully.<br><b>Please note this is only a trial version and is not fully compatible.</b>";
        $mo("#mo_message"+ otpType + formID).empty();
        $mo("#mo_message"+ otpType + formID).append(img);
        $mo("#mo_message"+ otpType + formID).show();
        disableOTPButton();
        $mo.ajax({
            url: moData.siteURL,
            type: "POST",
            data: {
                user_email: e,
                user_phone: e,
                otpType:otpType,
                security:moData.gnonce,
                action:moData.gaction,
            },
            crossDomain: !0, dataType: "json",
            success: function (o) {
                if (o.result === "success") {
                    //if otp was sent successfully
                    disableOTPButton();
                    $mo("#mo_message"+ otpType + formID).empty();
                    // $mo("#mo_message"+ otpType + formID).append(o.message);
                    $mo("#mo_message"+ otpType + formID).append(msg);
                    $mo("#mo_message"+ otpType + formID).css("border-top", "3px solid green");
                    $mo("#mo_verify_otp_button-container" + otpType + formID).show(),$mo('#mo_verify_field_container'+otpType + formID).show();
                } else {
                    // if otp wasn't sent successfully
                    disableOTPButton();
                    $mo("#mo_message"+ otpType + formID).empty();
                    $mo("#mo_message"+ otpType + formID).append(o.message);
                    $mo("#mo_message"+ otpType + formID).css("border-top", "3px solid red");
                }
            },
            error: function (o) {}
        });
    });
}

function bindVerifyButton(thisForm,formID,fieldID,fieldSelector,insertSelector,otpType,moData){
    // image HTML templates
    img = "<img alt='' src='" + moData.imgURL + "'>";

    $mo('#mo_verify_button_' + otpType + formID).click(function(){
        var e = $mo('#mo_verify_otp_' + otpType + formID).val();
        var f = fieldSelector.val();
        $mo("#mo_message"+ otpType + formID).empty();
        $mo("#mo_message"+ otpType + formID).append(img);
        $mo("#mo_message"+ otpType + formID).show();
        $mo.ajax({
            url: moData.siteURL,
            type: "POST",
            data: {
                user_email: f,
                user_phone: f,
                otp_token : e,
                otpType:otpType,
                security:moData.vnonce,
                action:moData.vaction,
            },
            crossDomain: !0, dataType: "json",
            success: function (o) {
                if (o.result === "success") {
                    //if otp was sent successfully
                    $mo("#mo_message"+ otpType + formID).hide();
                    $mo("#mo_verify_otp_button-container" + otpType + formID).hide(),$mo('#mo_verify_field_container'+otpType + formID).hide();
                    $mo("#mo_send_otp_" + otpType + formID).val("Resend OTP");
                    fieldSelector.addClass("mo-validated");
                    fieldSelector.focus().keyup(); // Mostly needed for ninja form
                    addValidatedIcon();
                } else {
                    // if otp wasn't sent successfully
                    $mo("#mo_message"+ otpType + formID).empty();
                    $mo("#mo_message"+ otpType + formID).append(o.message);
                    $mo("#mo_message"+ otpType + formID).css("border-top", "3px solid red");
                }
            },
            error: function (o) {}
        });
    });
}

function disableOTPButton(){
    if($mo.isFunction(window.moDisableOTPbutton)){
        moDisableOTPbutton();
    }
}
