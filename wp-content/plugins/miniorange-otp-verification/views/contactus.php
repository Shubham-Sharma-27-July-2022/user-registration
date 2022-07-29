<?php


echo'
               <div class="mo_registration_table_layout mo-otp-center" style="margin-top:2%">
                  <h3 style="text-align: center;"><b>'.mo_("CONTACT US").'</b></h3>
                  <hr>
                <h4 class="mo_otp_note" style="text-align: center;">'.mo_("Need any help? Just send us a query and we will get in touch.").'</h4>

                 <form name="f" method="post" action="" style="width:88%; margin-left: 40px;">
                        <input type="hidden" name="option" value="mo_validation_contact_us_query_option"/>
                            <div class="mo_support_input_container" style="border-bottom: none;"  >
                                <span class="mo_support_input_label" style="color: #000000;">Your Email</span>
                                <input type="email" class="mo_support_input" id="query_email" style="border: 1px solid #ddd;" name="query_email" value="'.mo_esc_string($email,"attr").'" placeholder="'.mo_("Enter your Email").'" required />
                            </div>
                             <div class="mo_support_input_container" style="border-bottom: none;">
                                <span class="mo_support_input_label" style="color: #000000;">Your Phone Number</span>
                                <input type="text" class="mo_support_input" name="query_phone" id="query_phone" style="border: 1px solid #ddd;" value="'.mo_esc_string($phone,"attr").'" 
                                    placeholder="'.mo_("Enter your phone").'"/>
                            </div>
                            <div class="mo_support_input_container" style="border-bottom: none;">
                                <span class="mo_support_input_label" style="color: #000000;">Enter Your Query</span>
                                    <textarea id="query" name="query" class="mo_support_text" 
                                        style="resize: vertical;border: 1px solid #ddd;width:100%" cols="52" rows="7"
                                        onkeyup="mo_registration_valid_query(this)" onblur="mo_registration_valid_query(this)" 
                                        onkeypress="mo_registration_valid_query(this)" 
                                        placeholder="'.mo_("Write your query...").'"></textarea>
                            </div>
                        <input type="submit" name="send_query" id="send_query" value="'.mo_("Submit Query"). '" 
                                class="mo_support_button" style="background-color: #4ba7ff;box-shadow: none;-webkit-box-shadow:none;" />
                    </form>
                    </div>
                        <script>
            function moSharingSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:10>t&&(e.value=10)
            }
            function moSharingSpaceValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:0>t&&(e.value=0)
            }
            function moLoginSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:20>t&&(e.value=20)
            }
            function moLoginSpaceValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:0>t&&(e.value=0)
            }
            function moLoginWidthValidate(e){
                var t=parseInt(e.value.trim());t>1000?e.value=1000:140>t&&(e.value=140)
            }
            function moLoginHeightValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:35>t&&(e.value=35)
            }
            jQuery(document).ready(function(){
                let sel = jQuery(".mo_support_input_container");
                sel.each(function(){
                    if(jQuery(this).find(".mo_support_input").val() !== "") 
                    jQuery(this).addClass("mo_has_value");
                });
                sel.focusout( function(){
                    if(jQuery(this).find(".mo_support_input").val() !== "") 
                        jQuery(this).addClass("mo_has_value");
                    else jQuery(this).removeClass("mo_has_value");
                });
            });
        </script>';