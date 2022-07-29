<?php

echo'

	 <div class="mo_registration_divided_layout" style="width:97%">
		<div class="mo_registration_table_layout mo-otp-full">
		    <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h2>'.mo_("CUSTOMIZE THE OTP POP-UPS").'
                        <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                data-show="false" 
                                data-toggle="design_instructions"></span>
                        </h2><hr/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"> 
                        <div class="mo_otp_note" id="design_instructions" style="color:#942828;">
                            '.mo_("<i> Configure your pop-ups below. Add scripts, images, css scripts or 
                                    change the popup entirely to your liking.</i>
                                    <br/><br/><b>NOTE:</b> Click on the Preview button to see how your pop up would look like.").'
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="mo_registration_table_layout mo-otp-full">
            <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h3>
                            <i>DEFAULT POPUP</i>
                            <span style="float:right;margin-top:-10px;">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large" 
                                        data-popup="mo_preview_popup" 
                                        data-iframe="defaultPreview" 
                                        value="'.mo_("Preview").'">
                                <input  type="button" 
                                        id="popupbutton"  '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large"
                                        data-popup="mo_popup_save" 
                                        data-iframe="defaultPreview" 
                                        value="'.mo_("Save").'">
                                <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                        data-show="false" 
                                        data-toggle="default_popup"></span>
                            </span>
                        </h3> <hr>
                    </td>
                </tr>
            </table>
            <div id="default_popup">
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <div class="mo_otp_note" style="color:#942828;">'.
                                mo_("Make sure to have the following tags in the popup: 
                                    <b>{{JQUERY}}</b>,
                                    <b>{{GO_BACK_ACTION_CALL}}</b>, 
                                    <b>{{FORM_ID}}</b>, 
                                    <b>{{OTP_FIELD_NAME}}</b>, 
                                    <b>{{REQUIRED_FIELDS}}</b>, 
                                    <b>{{REQUIRED_FORMS_SCRIPTS}}</b>").
                            '</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="45%">
                            <form   name="defaultPreview" 
                                    method="post" 
                                    action="'.admin_post_url().'" 
                                    target="defaultPreview">
                                <input type="hidden" id="popactionvalue" name="action" value="">
                                <input type="hidden" name="popuptype" value="'.mo_esc_string($default_template_type,"attr").'"> ';

                                wp_nonce_field( $nonce );
                                wp_editor($custom_default_popup , $editorId ,$templateSettings);

echo                '         
                        </td>
                        <td width="46%">
                                <iframe id="defaultPreview" 
                                        name="defaultPreview" 
                                        src="" style="width:100%;margin-top:1%;border-radius: 4px;background-color: #d8d7d7;height:467px">
                                </iframe>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mo_registration_table_layout mo-otp-full">
            <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h3>
                            <i>USER CHOICE POPUP</i>
                            <span style="float:right;margin-top:-10px;">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large" 
                                        data-popup="mo_preview_popup" 
                                        data-iframe="userchoicePreview" 
                                        value="'.mo_("Preview").'">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").'  
                                        class="button button-primary button-large"
                                        data-popup="mo_popup_save" 
                                        data-iframe="userchoicePreview" 
                                        value="'.mo_("Save").'">
                                <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                        data-show="false" 
                                        data-toggle="userchoice_popup">
                                </span>
                            </span>
                        </h3> <hr>
                    </td>
                </tr>
            </table>
            <div id="userchoice_popup">
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <div class="mo_otp_note" style="color:#942828;">'.
                                mo_("Make sure to have the following tags in the popup:
                                    <b>{{JQUERY}}</b>, 
                                    <b>{{GO_BACK_ACTION_CALL}}</b>, 
                                    <b>{{FORM_ID}}</b>, 
                                    <b>{{REQUIRED_FIELDS}}</b>, 
                                    <b>{{REQUIRED_FORMS_SCRIPTS}}</b>").
                            '</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="45%">
                            <form   name="userchoicePreview" 
                                    method="post" 
                                    action="'.admin_post_url().'" 
                                    target="userchoicePreview">
                                <input type="hidden" id="popactionvalue" name="action" value="">
                                <input type="hidden" name="popuptype" value="'.mo_esc_string($userchoice_template_type,"attr").'"> ';

                                wp_nonce_field( $nonce );

                                wp_editor($custom_userchoice_popup , $editorId2 ,$templateSettings2);

echo                '         
                        </td>
                        <td width="46%">
                                <iframe id="userchoicePreview" name="userchoicePreview" src="" 
                                    style="width:100%;margin-top:1%;border-radius: 4px;background-color: #d8d7d7;height:467px"></iframe>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mo_registration_table_layout mo-otp-full">
            <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h3>
                            <i>EXTERNAL POPUP</i>
                            <span style="float:right;margin-top:-10px;">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large" 
                                        data-popup="mo_preview_popup" 
                                        data-iframe="externalPreview" 
                                        value="'.mo_("Preview").'">
                                <input  type="button" id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large"
                                        data-popup="mo_popup_save" 
                                        data-iframe="externalPreview" 
                                        value="'.mo_("Save").'">
                                <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                        data-show="false" 
                                        data-toggle="external_popup"></span>
                            </span>
                        </h3> <hr>
                    </td>
                </tr>
            </table>
            <div id="external_popup">
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <div class="mo_otp_note" style="color:#942828;">'.
                                mo_("Make sure to have the following tags in the popup:
                                    <b>{{JQUERY}}</b>, 
                                    <b>{{GO_BACK_ACTION_CALL}}</b>, 
                                    <b>{{FORM_ID}}</b>, 
                                    <b>{{REQUIRED_FIELDS}}</b>, 
                                    <b>{{REQUIRED_FORMS_SCRIPTS}}</b>").
                            '</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="45%">
                            <form   name="externalPreview" 
                                    method="post" 
                                    action="'.admin_post_url().'" 
                                    target="externalPreview">
                                <input type="hidden" id="popactionvalue" name="action" value="">
                                <input type="hidden" name="popuptype" value="'.mo_esc_string($external_template_type,"attr").'"> ';

                                wp_nonce_field( $nonce );
                                wp_editor($custom_external_popup , $editorId3 ,$templateSettings3);

echo                '         
                        </td>
                        <td width="46%">
                                <iframe id="externalPreview" name="externalPreview" src=""
                                    style="width:100%;margin-top:1%;border-radius: 4px;background-color: #d8d7d7;height:467px"></iframe>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mo_registration_table_layout mo-otp-full">
            <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h3>
                            <i>ERROR POPUP</i>
                            <span style="float:right;margin-top:-10px;">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large" 
                                        data-popup="mo_preview_popup" 
                                        data-iframe="errorPreview" 
                                        value="'.mo_("Preview").'">
                                <input  type="button" 
                                        id="popupbutton" '.mo_esc_string($disabled,"attr").' 
                                        class="button button-primary button-large"
                                        data-popup="mo_popup_save" 
                                        data-iframe="errorPreview" 
                                        value="'.mo_("Save").'">
                                <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                        data-show="false" 
                                        data-toggle="error_popup"></span>
                            </span>
                        </h3> <hr>
                    </td>
                </tr>
            </table>
            <div id="error_popup">
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <div class="mo_otp_note" style="color:#942828;">'.
                                mo_("Make sure to have the following tags in the popup:
                                    <b>{{JQUERY}}</b>, 
                                    <b>{{GO_BACK_ACTION_CALL}}</b>, 
                                    <b>{{REQUIRED_FORMS_SCRIPTS}}</b>").
                            '</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="45%">
                            <form name="errorPreview" method="post" action="'.admin_post_url().'" target="errorPreview">
                                <input type="hidden" id="popactionvalue" name="action" value="">
                                <input type="hidden" name="popuptype" value="'.mo_esc_string($error_template_type,"attr").'"> ';

                                wp_nonce_field( $nonce );
                                wp_editor($error_popup , $editorId4 ,$templateSettings4);

echo                '         
                        </td>
                        <td width="46%">
                                <iframe id="errorPreview" name="errorPreview" 
                                    src=""
                                    style="width:100%;margin-top:1%;border-radius: 4px;background-color: #d8d7d7;height:467px"></iframe>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>';

echo '
		</div>
     </div>
     <script type="text/javascript">
        $mo = jQuery;
        $mo(document).ready(function(){    
            $mo("iframe").contents().find("body").append("'.$message.'");
            $mo("input:button[id=popupbutton]").click(function(){
                var iframe = $mo(this).data("iframe");
                var nonce = $mo("#_wpnonce").val();
                var popupAction = $mo(this).data("popup"); 
                var popupType = $mo("form[name="+iframe+"] input[name=\'popuptype\']").val();      
                var editorName = $mo("form[name="+iframe+"] textarea").attr("name");  
                var templatedata = $mo("form[name="+iframe+"] textarea").val();                           
                $mo("#"+iframe).contents().find("body").empty();
                $mo("#"+iframe).contents().find("body").append("'.$loaderimgdiv.'");
                var data = {form_name:iframe,popactionvalue:popupAction,popuptype: popupType,_wpnonce:nonce,action:popupAction};
                data[editorName] = templatedata;
                $mo.ajax({
                    url: "admin-post.php",
                    type:"POST",
                    data:data,
                    crossDomain:!0,
                    dataType:"json",
                    success:function(o){ 
                        $mo("#"+iframe).contents().find("body").empty();
                        $mo("#"+iframe).contents().find("body").append(o.message);
                    },
                    error:function(o,e,n){}
                });
            });
        });
    </script>';

